<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    protected $fillable = ['type', 'user_name', 'password'];
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];
    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
    public function initials(): string
    {
        if ($this->profile()->exists()) {
            return Str::of($this->profile->f_name_fa . '، ' . $this->profile->l_name_fa)
                ->explode('، ')
                ->map(fn(string $name) => Str::of($name)->substr(0, 1))
                ->implode(' ');
        }
        else{
            return Str::of($this->user_name)->substr(0, 2);
        }

        /*
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
        */
    }
    public function profile():HasOne
    {
        return $this->hasOne(Profile::class);
    }
    public function contacts():belongsToMany
    {
        return $this->belongsToMany(Contact::class);
    }
}
