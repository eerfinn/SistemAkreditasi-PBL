<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kriteria;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements CanResetPassword
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
        'email',
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
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's photo.
     */
    public function getPhotoAttribute($value)
    {
        return $value;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
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
