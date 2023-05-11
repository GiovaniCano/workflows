<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Sectionable extends MorphPivot
{
    public $incrementing = true;
}
