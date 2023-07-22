<?php

namespace Tests\Feature\workflows;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateDeleteTest extends FormTestAbstract
{
    use RefreshDatabase;

    public $main_user;
    public $workflow;
    
    public function setup(): void
    {
        parent::setUp();

        $this->seed();

        $this->main_user = User::findOrFail(1);
        $this->workflow = $this->main_user->workflows()->first();
    }

    public function test_edit_view_is_accessible_only_if_auth_and_is_owner_and_renders_correctly()
    {
        // no auth
        $response = $this->get(route('workflow.edit', $this->workflow));
        $response->assertRedirectToRoute('login');

        // no owner
        $user_noowner = User::findOrFail(2);
        $this->actingAs($user_noowner);
        $response = $this->get(route('workflow.edit', $this->workflow));
        $response->assertStatus(403);
        
        // all good
        $allSections = $this->workflow->nestedSections();
        
        $this->actingAs($this->main_user);
        $response = $this->get(route('workflow.edit', $this->workflow));
        $response->assertStatus(200);

        $response->assertSeeInOrder([
            '<aside',
            route('workflow.create'),
            $this->workflow->name,
            ...$allSections->pluck('name'),
            '</aside>',
        ], false);
        $response->assertSeeInOrder([
            '<main',
            '<form',
            route('workflow.destroy', $this->workflow),
            '"DELETE"',
            '</form>',

            '<form',
            route('workflow.update', $this->workflow),
            '<header',
            '<input',
            $this->workflow->name,
            'h1', // input class
            'type="submit"',
            '</header>',
            ...$allSections->pluck('name'),
            'type="submit"',
            '</form>',
            '</main>',
        ], false);
    }

    public function test_workflow_can_be_updated_if_auth_and_is_owner(): void
    {
        // no auth
        $response = $this->put(route('workflow.update', $this->workflow));
        $response->assertRedirectToRoute('login');

        // no owner
        $user_noowner = User::findOrFail(2);
        $this->actingAs($user_noowner);
        $response = $this->put(route('workflow.update', $this->workflow));
        $response->assertStatus(403);

        // all good
        $this->actingAs($this->main_user);
        $response = $this->put(route('workflow.update', $this->workflow), ['name' => 'wf name updated']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['workflow_url']);

        $this->workflow->refresh();
        $this->assertEquals('wf name updated', $this->workflow->name);
    }
    
    public function test_workflow_can_be_updated_with_valid_data(): void
    {
        $this->actingAs($this->main_user);

        Storage::fake();

        $maxLenAllowedText = str_repeat('abcdefghijklmnopqrst', 700); // 14,000 (20 * 700)
        
        /* create */
        $initialData = [
            'name' => 'Workflow',
            'images' => [
                'imageId' => UploadedFile::fake()->image('image.jpg')->size(1 * 1000),
                'imageIdToDelete' => UploadedFile::fake()->image('imageToDelete.jpg')->size(1.1 * 1000),
            ],
            'main_sections' => [
                [
                    'name' => 'Main Section',
                    'id' => 'stringId',
                    'position' => 0,
                    'content' => [
                        'sections' => [
                            [
                                'name' => 'Nested Section',
                                'id' => 'stringId',
                                'position' => 0,
                            ],
                            [
                                'name' => 'Delete this section',
                                'id' => 'stringId',
                                'position' => 1,
                            ]
                        ],
                        'minisections' => [
                            [
                                'name' => 'Mini Section',
                                'id' => 'stringId',
                                'position' => 2,
                            ]
                        ],
                        'wysiwygs' => [
                            [
                                'id' => 'stringId',
                                'position' => 3,
                                'content' => $maxLenAllowedText,
                            ]
                        ],
                        'images' => [
                            [
                                'id' => 'imageId',
                                'position' => 4
                            ],
                            [
                                'id' => 'imageIdToDelete',
                                'position' => 5
                            ]
                        ],
                    ]
                ]
            ]
        ];
        $response = $this->post(route('workflow.store'), $initialData, ['Accept' => 'application/json']);
        $response->assertStatus(201);
        
        $workflow = $this->main_user->workflows()->orderBy('id', 'desc')->first();
        
        $response->assertJson([
            'workflow_url' => route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()])
        ]);

        /* data */
        $initial_mainSection = $workflow->sections[0];
        $initial_nestedSection = $initial_mainSection->sections->filter(fn($section) => $section->type == 1)->first();
        $initial_miniSection = $initial_mainSection->sections->filter(fn($section) => $section->type == 2)->first();
        $initial_wysiwyg = $initial_mainSection->wysiwygs[0];

        $initial_image = $initial_mainSection->images[0];
        $initial_image_to_delete = $initial_mainSection->images[1];

        $initial_nestedSection_to_delete = $initial_mainSection->sections->filter(fn($section) => $section->name == 'Delete this section')->first();

        /* asserts */
        $this->assertDatabaseHas('sections', $workflow->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('sections', $initial_mainSection->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('sections', $initial_nestedSection->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('sections', $initial_miniSection->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('wysiwygs', $initial_wysiwyg->only('id', 'content'));
        $this->assertDatabaseHas('images', $initial_image->only('id', 'name'));

        Storage::assertExists($initial_image->name);

        $this->assertDatabaseHas('sections', ['name' => 'Delete this section']);
        Storage::assertExists($initial_image_to_delete->name);
        
        /* update */
        $initialData = [
            'name' => 'Updated Workflow',
            'images' => [
                'newImageId' => UploadedFile::fake()->image('image.jpg')->size(1.2 * 1000)
            ],
            'deleted' => [
                'sections' => [
                    $initial_nestedSection_to_delete->id
                ],
                'images' => [
                    $initial_image_to_delete->id
                ]
            ],
            'main_sections' => [
                [
                    'name' => 'New main section',
                    'id' => 'stringId',
                    'position' => 0,
                    'content' => [
                        'sections' => [],
                        'minisections' => [],
                        'wysiwygs' => [],
                        'images' => [],
                    ]
                ],
                [
                    'name' => 'Updated Main Section',
                    'id' => $initial_mainSection->id,
                    'position' => 1,
                    'content' => [
                        'sections' => [
                            [
                                'name' => 'Updated Nested Section',
                                'id' => $initial_nestedSection->id,
                                'position' => 4,
                            ]
                        ],
                        'minisections' => [
                            [
                                'name' => 'Updated Mini Section',
                                'id' => $initial_miniSection->id,
                                'position' => 3,
                            ]
                        ],
                        'wysiwygs' => [
                            [
                                'id' => $initial_wysiwyg->id,
                                'position' => 2,
                                'content' => '<p>updated content</p>',
                            ]
                        ],
                        'images' => [
                            [
                                'id' => $initial_image->id,
                                'position' => 1
                            ],
                            [
                                'id' => 'newImageId',
                                'position' => 0
                            ]
                        ],
                    ]
                ],
            ]
        ];
        $response = $this->put(route('workflow.update', $workflow), $initialData, ['Accept' => 'application/json']);
        $response->assertStatus(200);

        $workflow->refresh();

        $response->assertJson([
            'workflow_url' => route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()])
        ]);

        /* data */
        $updated_main_section = $workflow->sections->firstWhere('name', 'Updated Main Section');
        $new_image = $updated_main_section->images->firstWhere('position', 0);

        /* asserts */
        Storage::assertExists($initial_image->name); // still exists

        $this->assertDatabaseHas('sections', ['name' => 'New main section', 'position' => 0]);
        Storage::assertExists($new_image->name);

        $this->assertDatabaseHas('sections', ['name' => 'Updated Workflow']);
        $this->assertDatabaseHas('sections', ['name' => 'Updated Main Section', 'position' => 1]);
        $this->assertDatabaseHas('sections', ['name' => 'Updated Nested Section', 'position' => 4]);
        $this->assertDatabaseHas('sections', ['name' => 'Updated Mini Section', 'position' => 3]);
        $this->assertDatabaseHas('wysiwygs', ['content' => '<p>updated content</p>', 'position' => 2]);
        $this->assertDatabaseHas('images', $new_image->only('id', 'name'));

        $this->assertDatabaseMissing('sections', ['name' => 'Delete this section']);
        Storage::assertMissing($initial_image_to_delete->name);
    }

    public function test_workflow_cant_be_updated_with_invalid_data(): void
    {
        $this->assertInvalidSubmit(
            [
                'name' => '',
                'images' => 'not an array',
                'main_sections' => 'not an array',
            ], 
            ['name', 'images', 'main_sections']
        );

        $this->assertInvalidSubmit(
            [
                'name' => 'More than 25 characters length',
                'images' => ['not a file', 4],
                'main_sections' => ['not a json string', 4],
            ], 
            ['name', 'images.0', 'images.1', 'main_sections.0', 'main_sections.1']
        );

        $this->assertInvalidSubmit(
            [
                'name' => $this->main_user->workflows()->orderBy('id', 'DESC')->first()->name, // last workflow name (unique)
                'images' => [
                    UploadedFile::fake()->image('noimage.pdf'), 
                    UploadedFile::fake()->image('image.jpg')->size(2.1*1000),
                ]
            ], 
            ['name', 'images.0', 'images.1']
        );

        $this->validateMainSections();
    }

    public function test_owner_can_delete_owned_workflow(): void
    {
        // no auth
        $response = $this->put(route('workflow.destroy', $this->workflow));
        $response->assertRedirectToRoute('login');

        // no owner
        $user_noowner = User::findOrFail(2);
        $this->actingAs($user_noowner);
        $response = $this->put(route('workflow.destroy', $this->workflow));
        $response->assertStatus(403);

        // all good
        $this->actingAs($this->main_user);

        $response = $this->delete(route('workflow.destroy', $this->workflow));
        $response->assertRedirectToRoute('workflow.index');

        $theOtherWorkflow = $this->main_user->workflows()->first();
        $response = $this->delete(route('workflow.destroy', $theOtherWorkflow));
        $response->assertRedirectToRoute('workflow.index');

        $this->assertDatabaseMissing('sections', ['user_id' => $this->main_user->id]);
        $this->assertDatabaseMissing('images', ['user_id' => $this->main_user->id]);
        $this->assertDatabaseMissing('wysiwygs', ['user_id' => $this->main_user->id]);
    }

    //////////////////////////////////////////////////////////////////

    public function assertInvalidSubmit(array $data, array $errors): void {
        $this->actingAs($this->main_user);
        $response = $this->put(route('workflow.update', $this->workflow), $data, ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertInvalid($errors);
    }
}
