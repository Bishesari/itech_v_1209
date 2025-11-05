<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['chapter_id', 'text', 'difficulty', 'is_final'];
    public function chapter():BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
    public function options():HasMany
    {
        return $this->hasMany(Option::class);
    }
    public const CLUSTERS = [
        'easy' => 'ساده',
        'medium' => 'متوسط',
        'hard' => 'سخت',
    ];
}
