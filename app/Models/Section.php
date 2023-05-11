<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Sections inside the current section
     */
    public function sections() {
        return $this->morphedByMany(Section::class, 'sectionable')
            ->using(Sectionable::class)
            ->withTimestamps()
            ->withPivot(['position']);
    }

    public function images() {
        return $this->morphedByMany(Image::class, 'sectionable')
            ->using(Sectionable::class)
            ->withTimestamps()
            ->withPivot(['position']);
    }

    public function wysiwygs() {
        return $this->morphedByMany(Wysiwyg::class, 'sectionable')
            ->using(Sectionable::class)
            ->withTimestamps()
            ->withPivot(['position']);
    }
}
