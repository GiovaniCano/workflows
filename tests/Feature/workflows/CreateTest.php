<?php

namespace Tests\Feature\workflows;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateTest extends FormTestAbstract
{
    use RefreshDatabase, WithFaker;

    public function test_create_view_is_accessible_only_if_auth_and_renders_correctly(): void
    {
        // no auth
        $response = $this->get(route('workflow.create'));
        $response->assertRedirectToRoute('login');

        // auth
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('workflow.create'));
        $response->assertStatus(200);

        $response->assertSeeInOrder([
            '<aside',
            '</aside>',
        ], false);
        $response->assertSeeInOrder([
            '<main',
            '<form',
            route('workflow.store'),
            '<header',
            '<input',
            'h1', // input class
            'type="submit"',
            '</header>',
            'type="submit"',
            '</form>',
            '</main>',
        ], false);
    }

    public function test_workflow_can_be_created_if_auth(): void
    {
        // no auth
        $response = $this->post(route('workflow.store'));
        $response->assertRedirectToRoute('login');

        // auth
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('workflow.store', ['name' => 'wf name']));
        $response->assertStatus(201);
        $response->assertJsonStructure(['workflow_url']);

        $workflow = $user->workflows()->latest()->first();
        $this->assertEquals('wf name', $workflow->name);
    }

    public function test_workflow_can_be_created_with_valid_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake();

        $maxLenAllowedText = str_repeat('abcdefghijklmnopqrst', 700); // 14,000 (20 * 700)

        $data = [
            'name' => 'Workflow',
            'images' => [
                'imageId' => UploadedFile::fake()->image('image.jpg')->size(1.2 * 1000)
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
                            ]
                        ],
                        'minisections' => [
                            [
                                'name' => 'Mini Section',
                                'id' => 'stringId',
                                'position' => 1,
                            ]
                        ],
                        'wysiwygs' => [
                            [
                                'id' => 'stringId',
                                'position' => 2,
                                'content' => $maxLenAllowedText,
                            ]
                        ],
                        'images' => [
                            [
                                'id' => 'imageId',
                                'position' => 3
                            ]
                        ],
                    ]
                ]
            ]
        ];
        $response = $this->post(route('workflow.store'), $data, ['Accept' => 'application/json']);

        $response->assertStatus(201);
        
        $workflow = $user->workflows()->latest()->first();
        
        $response->assertJson([
            'workflow_url' => route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()])
        ]);

        $mainSection = $workflow->sections[0];
        $nestedSection = $mainSection->sections->filter(fn($section) => $section->type == 1)->first();
        $miniSection = $mainSection->sections->filter(fn($section) => $section->type == 2)->first();
        $wysiwyg = $mainSection->wysiwygs[0];
        $image = $mainSection->images[0];

        $this->assertDatabaseHas('sections', $workflow->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('sections', $mainSection->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('sections', $nestedSection->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('sections', $miniSection->only('id', 'name' ,'type', 'user_id'));
        $this->assertDatabaseHas('wysiwygs', $wysiwyg->only('id', 'content'));
        $this->assertDatabaseHas('images', $image->only('id', 'name'));

        Storage::assertExists($image->name);
    }

    public function test_workflow_cant_be_created_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

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
                'images' => [
                    UploadedFile::fake()->image('noimage.pdf'), 
                    UploadedFile::fake()->image('image.jpg')->size(2.1*1000),
                ]
            ], 
            ['name', 'images.0', 'images.1']
        );

        $this->validateMainSections();
    }

    //////////////////////////////////////////////////////////////////

    public function assertInvalidSubmit(array $data, array $errors): void {
        $response = $this->post(route('workflow.store'), $data, ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertInvalid($errors);
    }
}
