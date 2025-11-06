<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Institute extends Model
{
    protected $fillable = ['short_name', 'full_name', 'abb', 'remain_credit'];
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'institute_role_user')
            ->withPivot(['user_id', 'assigned_by', 'assigned_at'])
            ->withTimestamps();
    }
}
