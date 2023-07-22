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
        'type',
        'position',
        'section_id',
    ];

    /**
     * To quickly get unwanted relations (keep sync with $with property)
     */
    public static $without = ['sections', 'images', 'wysiwygs'];
    protected $with = ['sections', 'images', 'wysiwygs'];

    /**
     * @var \Illuminate\Support\Collection<TKey, TValue>
     */
    private Collection $nested_sections;

    protected static function booted()
    {
        static::creating(function($section) {
            if(auth()->check()) {
                $section->user_id = auth()->id();
            }
        });
    }

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

    public function parent_section() {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function sections() {
        return $this->hasMany(Section::class, 'section_id')->orderBy('position');
    }

    public function images() {
        return $this->hasMany(Image::class)->orderBy('position');
    }

    public function wysiwygs() {
        return $this->hasMany(Wysiwyg::class)->orderBy('position');
    }

    /**
     * Get sections, images and wysiwygs of this sections
     */
    public function getAllContent() {
        return $this->sections->concat($this->wysiwygs)->concat($this->images)
            ->sortBy('position', SORT_NATURAL)->values();
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
