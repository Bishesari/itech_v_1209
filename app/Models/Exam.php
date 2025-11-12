<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    protected $fillable = ['standard_id', 'title', 'question_count', 'start_date', 'end_date'];

    public function questions():BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')->withTimestamps();
    }

    public function standard():BelongsTo
    {
        return $this->belongsTo(Standard::class);

    }

}
