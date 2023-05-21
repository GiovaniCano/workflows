<?php

namespace App\Models;

use Illuminate\Support\Str;
use Database\Factories\SectionFactory;

class Workflow extends Section
{
    protected $table = 'sections';

    protected $attributes = [
        'type' => 0,
    ];

    public function make_slug() {
        $slug = Str::slug($this->name);
        if(strlen($slug) > 255) $slug = substr($slug, 0, 255);
        return $slug;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if($key !== 'type') parent::setAttribute($key, $value);
    }
    
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory()
    {
        return SectionFactory::new();
    }
}
