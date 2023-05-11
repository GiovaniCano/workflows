<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wysiwyg>
 */
class WysiwygFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = '';
        $content .= "<h2>".fake()->title()."</h2>";
        foreach (fake()->paragraphs(10) as $p) {
            $content .= "<p>{$p}</p>";
        }

        return [
            'content' => $content
        ];
    }
}
