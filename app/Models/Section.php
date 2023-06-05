<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    protected $with = ['sections', 'images', 'wysiwygs'];
    
    /**
     * @var \Illuminate\Support\Collection<TKey, TValue>
     */
    private Collection $nested_sections;

    /**
     * @return string The slug version of the section's name
     */
    public function make_slug(): string
    {
        $slug = Str::slug($this->name);
        if (strlen($slug) > 25) $slug = substr($slug, 0, 25);
        return $slug;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Sections inside the current section
     */
    public function sections() {
        return $this->morphedByMany(Section::class, 'sectionable', null, 'section_id')
            ->using(Sectionable::class)
            ->withTimestamps()
            ->withPivot(['position'])
            ->orderBy('pivot_position');
    }

    public function images() {
        return $this->morphedByMany(Image::class, 'sectionable', null, 'section_id')
            ->using(Sectionable::class)
            ->withTimestamps()
            ->withPivot(['position'])
            ->orderBy('pivot_position');
    }

    public function wysiwygs() {
        return $this->morphedByMany(Wysiwyg::class, 'sectionable', null, 'section_id')
            ->using(Sectionable::class)
            ->withTimestamps()
            ->withPivot(['position'])
            ->orderBy('pivot_position');
    }

    /**
     * Get sections, images and wysiwygs of this sections
     */
    public function getAllContent() {
        return $this->sections->concat($this->wysiwygs)->concat($this->images)
            ->sortBy('pivot.position', SORT_NATURAL)->values();
    }

    /**
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    public function nestedSections(): Collection {
        $this->nested_sections = collect([]);

        $this->findNestedSections($this->sections);

        return $this->nested_sections;
    }

    /**
     * @param \Illuminate\Support\Collection<TKey, TValue> $sections
     */
    private function findNestedSections($sections): void {
        $sections->each(function($section) {
            $this->nested_sections = $this->nested_sections->push($section);
            $this->findNestedSections($section->sections);
        });
    }
}
