<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kriteria;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the user's photo.
     */
    public function getPhotoAttribute($value)
    {
        return $value;
    }

    public function isAdmin()
    {
        return $this->role === 'administrator';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isDosen()
    {
        return $this->role === 'dosen';
    }

    public function kriteria()
    {
        if ($this->role === 'dosen1') {
            return Kriteria::whereIn('id', [1, 2, 3])->get();
        } elseif ($this->role === 'dosen2') {
            return Kriteria::whereIn('id', [4, 5, 6])->get();
        } elseif ($this->role === 'dosen3') {
            return Kriteria::whereIn('id', [7, 8, 9])->get();
        } elseif ($this->role === 'administrator') {
            return Kriteria::all();
        } else {
            return collect();
        }
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
}
