<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ExamUser extends Pivot
{
    public function answers():HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
