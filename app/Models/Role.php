<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name_fa', 'name_en', 'created', 'updated'];
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'institute_role_user')
            ->withPivot(['institute_id', 'assigned_by', 'assigned_at']);
    }
    public function institutes(): BelongsToMany
    {
        return $this->belongsToMany(Institute::class, 'institute_role_user')
            ->withPivot(['user_id', 'assigned_by', 'assigned_at']);
    }
}
