<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'position',
        'section_id',
    ];

    protected static function booted(): void
    {
        static::creating(function($image) {
            if(auth()->check()) {
                $image->user_id = auth()->id();
            }
        });
    }

    public function section() {
        return $this->belongsTo(Section::class);
    }
}
