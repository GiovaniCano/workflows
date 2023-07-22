<?php

namespace App\Models;

use Database\Factories\SectionFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class Workflow extends Section
{
    protected $table = 'sections';

    protected $attributes = [
        'type' => 0,
        'position' => 0,
        'section_id' => null,
    ];

    protected $with = ['sections'];

    protected static function booted(): void
    {
        parent::booted();

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
        if ($key !== 'type' && $key !== 'position' && $key !== 'section_id') parent::setAttribute($key, $value);
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
        throw new Exception('Workflows can not have images');
    }
    /**
     * Workflows can not have wysiwygs
     */
    public function wysiwygs() {
        throw new Exception('Workflows can not have wysiwygs');
    }
    /**
     * Workflows can only have sections
     */
    public function getAllContent() {
        throw new Exception('Workflows can only have sections');
    }
}
