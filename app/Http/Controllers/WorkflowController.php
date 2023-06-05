<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Http\Requests\WorkflowRequest;

class WorkflowController extends Controller
{
    public function __construct()
    {   
        $this->authorizeResource(Workflow::class);
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
        $workflow = new Workflow;
        return view('workflows.form', compact('workflow'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkflowRequest $request)
    {
        //
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
        request()->user()->update(['last_used_workflow_id' => $workflow->id]);
        return view('workflows.form', compact('workflow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkflowRequest $request, Workflow $workflow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workflow $workflow)
    {
        dd($workflow);
    }
}
