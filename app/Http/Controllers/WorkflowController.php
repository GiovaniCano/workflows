<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Section;
use App\Models\Wysiwyg;
use App\Models\Workflow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\WorkflowRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class WorkflowController extends Controller
{
    /**
     * @var \Illuminate\Http\UploadedFile[]
     */
    public array $uploadedImages = [];

    public array $uploadedImagesNames = [];

    public array $templatesModels;

    public function __construct()
    {   
        $this->authorizeResource(Workflow::class);

        $this->templatesModels = [
            'section' => new Section([
                'type' => 1,
                'name' => '',
            ]),
            'minisection' => new Section([
                'type' => 2,
                'name' => '',
            ]),
            'image' => new Image,
            'wysiwyg' => new Wysiwyg,
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $last_used_workflow_id = auth()->user()->last_used_workflow_id; // foreignId: nullOnDelete
        if (!is_null($last_used_workflow_id)) {
            $last_modified_workflow = Workflow::findOrFail($last_used_workflow_id);
            return to_route('workflow.show', [
                'workflow' => $last_used_workflow_id,
                'slug' => $last_modified_workflow->make_slug()
            ]);
        }

        $last_modified_workflow = Workflow::where(['user_id' => auth()->id()])->latest()->first();
        if (!is_null($last_modified_workflow)) {
            return to_route('workflow.show', [
                'workflow' => $last_modified_workflow,
                'slug' => $last_modified_workflow->make_slug()
            ]);
        }

        return view('workflows.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templatesModels = $this->templatesModels;
        $workflow = new Workflow;
        return view('workflows.form', compact('workflow', 'templatesModels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkflowRequest $request)
    {
        $validated = $request->validated();

        $this->uploadedImages = $validated['images'] ?? [];

        // save models
        $workflow = DB::transaction(function () use ($validated) {
            $workflow = Workflow::create(['name' => $validated['name']]);

            foreach ($validated['main_sections'] ?? [] as $mainSection_data) {
                $mainSection_data['type'] = 1;
                $this->createAndAttachSectionAndContent($mainSection_data, $workflow);
            }

            return $workflow;
        }, 3);

        // save images
        foreach ($this->uploadedImagesNames as $id => $name) {
            $file = $this->uploadedImages[$id];
            // Storage::putFileAs('', $file, $name);
            Storage::put($name, file_get_contents($file));
        }

        return response()->json([
            'workflow_url' => route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workflow $workflow)
    {
        request()->user()->update(['last_used_workflow_id' => $workflow->id]);
        return view('workflows.show', compact('workflow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workflow $workflow)
    {
        $templatesModels = $this->templatesModels;
        request()->user()->update(['last_used_workflow_id' => $workflow->id]);
        return view('workflows.form', compact('workflow', 'templatesModels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkflowRequest $request, Workflow $workflow)
    {
        $validated = $request->validated();

        $this->uploadedImages = $validated['images'] ?? [];
        
        DB::transaction(function () use ($workflow, $validated) {
            // workflow name
            $workflow->name = $validated['name'];
            if($workflow->isDirty('name')) $workflow->save();

            // update or create
            foreach ($validated['main_sections'] ?? [] as $mainSection_data) {
                $mainSection_data['type'] = 1;
                $this->updateOrCreateAndAttachSectionAndContent($mainSection_data, $workflow);
            }

            // delete records
            $deleted_images = $validated['deleted']['images'] ?? [];
            $images = Image::whereIn('id', $deleted_images)->pluck('name')->toArray();
            Storage::delete($images);
            Image::destroy($deleted_images);

            $deleted_wysiwygs = $validated['deleted']['wysiwygs'] ?? [];
            Wysiwyg::destroy($deleted_wysiwygs);

            $deleted_sections = $validated['deleted']['sections'] ?? [];
            Section::destroy($deleted_sections);
        }, 3);

        // save images
        foreach ($this->uploadedImagesNames as $id => $name) {
            $file = $this->uploadedImages[$id];
            // Storage::putFileAs('', $file, $name);
            Storage::put($name, file_get_contents($file));
        }

        return response()->json([
            'workflow_url' => route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workflow $workflow)
    {
        DB::transaction(function() use ($workflow) {    
            $images = $this->getAllImagesNames($workflow);

            Storage::delete($images);

            $workflow->delete();
        });

        return to_route('workflow.index');
    }

    //////////////////////////////////////////////////////////////////////////

    /**
     * Create or update a section an attach it to the parent
     * @param array $section_data Data to create or update the section and its content
     * @param \App\Models\Section $parent Parent section
     */
    protected function updateOrCreateAndAttachSectionAndContent(array $section_data, Section $parent): void {
        $is_int = filter_var($section_data['id'], FILTER_VALIDATE_INT);
        if($is_int) {
            // update
            $section = Section::findOrFail($section_data['id']);
            $section->name = $section_data['name'];
            $section->position = $section_data['position'];
            $section->section_id = $parent->id;
            if($section->isDirty()) $section->save();
        } else {
            // create
            $section = Section::create([
                'name' => $section_data['name'],
                'type' => $section_data['type'],
                'position' => $section_data['position'],
                'section_id' => $parent->id,
            ]);
        }

        // iterate content
        foreach ($section_data['content'] ?? [] as $type => $content) {
            switch ($type) {
                case 'wysiwygs':
                    foreach ($content as $wysiwyg_data) {
                        $is_int = filter_var($wysiwyg_data['id'], FILTER_VALIDATE_INT);
                        if($is_int) {
                            Wysiwyg::where('id', $wysiwyg_data['id'])->update([
                                'content' => $wysiwyg_data['content'],
                                'position' => $wysiwyg_data['position'],
                                'section_id' => $section->id,
                            ]);
                        } else {
                            Wysiwyg::create([
                                'content' => $wysiwyg_data['content'],
                                'position' => $wysiwyg_data['position'],
                                'section_id' => $section->id,
                            ]);
                        }
                    }
                    break;
                case 'images':
                    foreach ($content as $image_data) {
                        $is_int = filter_var($image_data['id'], FILTER_VALIDATE_INT);
                        if($is_int) {
                            Image::where('id', $image_data['id'])->update([
                                'position' => $image_data['position'],
                                'section_id' => $section->id,
                            ]);
                        } else {
                            $fileId = $image_data['id'];
                            $extension = $this->uploadedImages[$fileId]->getClientOriginalExtension();
                            $name =  Str::uuid()->toString() . '.' . $extension;
    
                            $this->uploadedImagesNames[$fileId] = $name;
    
                            Image::create([
                                'name' => $name,
                                'position' => $image_data['position'],
                                'section_id' => $section->id,
                            ]);
                        }
                    }
                    break;
                case 'sections':
                    foreach ($content as $section_data) {
                        $section_data['type'] = 1;
                        $this->updateOrCreateAndAttachSectionAndContent($section_data, $section);
                    }
                    break;      
                case 'minisections':
                    foreach ($content as $minisection_data) {
                        $minisection_data['type'] = 2;
                        $this->updateOrCreateAndAttachSectionAndContent($minisection_data, $section);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Create a section an attach it to the parent
     * @param array $section_data Data to create the section and its content
     * @param \App\Models\Section $parent Parent section
     */
    protected function createAndAttachSectionAndContent(array $section_data, Section $parent): void {
        // create section
        $section = Section::create([
            'name' => $section_data['name'],
            'type' => $section_data['type'],
            'position' => $section_data['position'],
            'section_id' => $parent->id,
        ]);

        // iterate content
        foreach ($section_data['content'] ?? [] as $type => $content) {
            switch ($type) {
                case 'wysiwygs':
                    foreach ($content as $wysiwyg_data) {
                        Wysiwyg::create([
                            'content' => $wysiwyg_data['content'],
                            'position' => $wysiwyg_data['position'],
                            'section_id' => $section->id,
                        ]);
                    }
                    break;
                case 'images':
                    foreach ($content as $image_data) {
                        $fileId = $image_data['id'];
                        $extension = $this->uploadedImages[$fileId]->getClientOriginalExtension();
                        $name =  Str::uuid()->toString() . '.' . $extension;

                        $this->uploadedImagesNames[$fileId] = $name;

                        Image::create([
                            'name' => $name,
                            'position' => $image_data['position'],
                            'section_id' => $section->id,
                        ]);
                    }
                    break;
                case 'sections':
                    foreach ($content as $section_data) {
                        $section_data['type'] = 1;
                        $this->createAndAttachSectionAndContent($section_data, $section);
                    }
                    break;      
                case 'minisections':
                    foreach ($content as $minisection_data) {
                        $minisection_data['type'] = 2;
                        $this->createAndAttachSectionAndContent($minisection_data, $section);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Get all nested images names in the section
     * @param \App\Models\Section $section
     * @return Array Array of images names
     */
    protected function getAllImagesNames(Section $section): Array {
        if($section->type === 0) { // Workflows can not have images
            $images = [];
        } else {
            $images = $section->images->pluck('name')->toArray();
        }

        foreach ($section->sections as $section) {
            $images = array_merge($images, $this->getAllImagesNames($section));
        }
        
        return $images;
    }
}
