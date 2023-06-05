<?php

namespace App\Models;

use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Builder;

class Workflow extends Section
{
    protected $table = 'sections';

    protected $attributes = [
        'type' => 0,
    ];

    protected $with = ['sections'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('workflow_type', function (Builder $builder) {
            $builder->where('type', 0);
        });
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
        if ($key !== 'type') parent::setAttribute($key, $value);
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

    /**
     * Workflows can not have images
     */
    public function images() {
        abort(500, 'Workflows can not have images');
    }
    /**
     * Workflows can not have wysiwygs
     */
    public function wysiwygs() {
        abort(500, 'Workflows can not have wysiwygs');
    }
    /**
     * Workflows can only have sections
     */
    public function getAllContent() {
        abort(500, 'Workflows can only have sections');
    }
}
