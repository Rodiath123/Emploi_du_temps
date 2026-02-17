<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Auditable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        // Adjust this based on your user role system
        // Examples:
        // return $this->role === 'admin';
        // return $this->is_admin === true;
        // return in_array($this->role, ['admin', 'super_admin']);
        
        // For now, we'll check if user has an 'admin' or 'is_admin' attribute
        return isset($this->attributes['role']) && $this->attributes['role'] === 'admin'
               || isset($this->attributes['is_admin']) && $this->attributes['is_admin'];
    }
}
