<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wysiwyg extends Model
{
    use HasFactory;
    
    public function sections() {
        return $this->morphToMany(Section::class, 'sectionable');
    }
}
