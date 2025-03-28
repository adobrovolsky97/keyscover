<?php

namespace App\Models\User;

use App\Enums\Role\Role;
use App\Models\Order\Order;
use App\Models\ResetPasswordCode\ResetPasswordCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone',
        'surname',
        'name',
        'email',
        'provider',
        'provider_id',
        'password',
        'last_activity_at',
        'role',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => Role::class
    ];

    public function getFullNameAttribute(): string
    {
        $surnameAndName = [$this->surname, $this->name];

        return trim(implode(' ', array_filter($surnameAndName)));
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function resetPasswordCodes(): HasMany
    {
        return $this->hasMany(ResetPasswordCode::class, 'user_id');
    }
}
