<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Image;
use App\Models\Section;
use App\Models\User;
use App\Models\Workflow;
use App\Models\Wysiwyg;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $state_callback = fn($attr, Section $section) => ['user_id' => $section->user_id, 'section_id' => $section->id];

        $mini_sections_factory = Section::factory(3, ['type' => 2])
            ->has(
                Wysiwyg::factory(1)->state($state_callback)->state(['position'=>0])
            )
            ->has(
                Image::factory(3)->state($state_callback)->state(new Sequence(['position'=>1],['position'=>2],['position'=>3]))
            );

        $nested_sections_factory = Section::factory(2)
            ->has(
                Wysiwyg::factory(1)->state($state_callback)->state(['position'=>0])
            )
            ->has(
                $mini_sections_factory->state($state_callback)->state(new Sequence(['position'=>1],['position'=>2],['position'=>3]))
            );

        $main_sections_factory = Section::factory(2)
            ->has(
                Wysiwyg::factory(1)->state($state_callback)->state(['position' => 0])
            )
            ->has(
                Image::factory(3)->state($state_callback)->state(new Sequence(['position'=>1],['position'=>2],['position'=>3]))
            )
            ->has(
                $nested_sections_factory->state($state_callback)->state(new Sequence(['position'=>4],['position'=>5]))
            )
            ->has(
                Image::factory(2)->state($state_callback)->state(new Sequence(['position'=>6],['position'=>7]))
            );

        $workflow_factory = Workflow::factory(2, ['type' => 0, 'position' => 0, 'section_id' => null])
            ->has(
                $main_sections_factory->state($state_callback)->state(new Sequence(['position'=>0],['position'=>1]))
            );

        User::factory(2)
            ->has($workflow_factory, 'workflows')
            ->create();
    }
}
