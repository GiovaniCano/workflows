<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wysiwyg extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'position',
        'section_id',
    ];
   
    protected static function booted(): void
    {
        static::creating(function($wysiwyg) {
            if(auth()->check()) {
                $wysiwyg->user_id = auth()->id();
            }
        });
    }
    
    public function section() {
        return $this->belongsTo(Section::class);
    }
}
