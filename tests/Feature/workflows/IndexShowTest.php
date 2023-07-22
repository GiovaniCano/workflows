<?php

namespace Tests\Feature\workflows;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class IndexShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    public function test_index_is_not_accessible_for_guests()
    {
        $res = $this->get(route('workflow.index'));
        $res->assertRedirectToRoute('login');
    }

    public function test_index_is_accessible_when_no_workflows_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $res = $this->get(route('workflow.index'));
        $res->assertStatus(200);
        $res->assertSee(route('workflow.create'), false);
    }
    
    public function test_index_redirects_to_recent_workflow_when_workflows_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $first_workflow = $user->workflows()->create(['type' => 0, 'name' => 'first workflow']);
        $res = $this->get(route('workflow.index'));
        $res->assertRedirectToRoute('workflow.show', ['workflow' => $first_workflow, 'slug' => $first_workflow->make_slug()]);
        
        $second_workflow = $user->workflows()->create(['type' => 0, 'name' => 'this is another name']);
        $user->update(['last_used_workflow_id' => $second_workflow->id]);
        
        $res = $this->get(route('workflow.index'));
        $res->assertRedirectToRoute('workflow.show', ['workflow' => $second_workflow, 'slug' => $second_workflow->make_slug()]);
    }

    public function test_workflow_show_route_renders_correctly(): void
    {
        Artisan::call('db:seed', ['--class=TestDatabaseSeeder']);

        $user = User::with('workflows')->findOrFail(1);
        $workflow = $user->workflows[0];
        $allSections = $workflow->nestedSections();

        $response = $this->actingAs($user)->get(route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()]));
        $response->assertStatus(200);
        
        $response->assertSeeInOrder([
            '<aside',
            route('workflow.create'),
            $workflow->name,
            ...$allSections->pluck('name'),
            '</aside>',
        ], false);
        $response->assertSeeInOrder([
            '<main',
            '<h1',
            $workflow->name,
            '</h1>',
            ...$allSections->pluck('name'),
            '</main>',
        ], false);
    }

    public function test_only_owner_can_see_workflow()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $ownerWorkflow = $owner->workflows()->create(['type' => 0, 'name' => 'Owner Workflow', 'user_id' => $owner->id]);
        
        $this->actingAs($owner);
        $response = $this->get(route('workflow.show', ['workflow' => $ownerWorkflow, 'slug' => $ownerWorkflow->make_slug()]));
        $response->assertStatus(200);
        
        $this->actingAs($otherUser);
        $response = $this->get(route('workflow.show', ['workflow' => $ownerWorkflow, 'slug' => $ownerWorkflow->make_slug()]));
        $response->assertStatus(403);
    }
}