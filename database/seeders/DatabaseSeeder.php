<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Image;
use App\Models\Section;
use App\Models\User;
use App\Models\Workflow;
use App\Models\Wysiwyg;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user_id = User::factory(1)->create([
            'email' => 'q@q.q'
        ])[0]->id;

        Workflow::factory(2)->state(['type'=>0,'user_id'=>$user_id]) // 2 workflows per user
            ->hasAttached( 
                Section::factory(2)->state(['type'=>1,'user_id'=>$user_id]) // 3 sections per workflow
                    ->hasAttached(Wysiwyg::factory(1), ['position'=>0])
                    ->hasAttached(Image::factory(3), new Sequence(['position'=>1],['position'=>2],['position'=>3]))
                    ->hasAttached(
                        Section::factory(2)->state(['type'=>1,'user_id'=>$user_id]) // 2 sections per section
                            ->hasAttached(Wysiwyg::factory(1), new Sequence(['position'=>0]))
                            ->hasAttached(
                                Section::factory(3)->state(['type'=>2,'user_id'=>$user_id]) // 3 mini sections per section
                                    ->hasAttached(Wysiwyg::factory(1), new Sequence(['position'=>0])),
                                new Sequence(['position'=>1],['position'=>2],['position'=>3])
                            ),
                        new Sequence(['position'=>4],['position'=>5])
                    ),
                    // ->hasAttached(Wysiwyg::factory(1), ['position'=>6]),
                new Sequence(['position'=>0],['position'=>1])
            )
            ->create();
    }
}
