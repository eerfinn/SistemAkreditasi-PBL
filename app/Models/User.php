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
        'photo',
        'kriteria_access'
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
        'kriteria_access' => 'array',
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

    /**
     * Get the kriteria that the user has access to.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function kriteria()
    {
        // Admin and non-dosen roles have access to all kriteria
        if ($this->role === 'administrator' || $this->role !== 'dosen') {
            return Kriteria::all();
        }
        
        // For dosen with kriteria_access, return those specific kriteria
        if (!empty($this->kriteria_access)) {
            return Kriteria::whereIn('id', $this->kriteria_access)->get();
        }
        
        return collect();
    }

    /**
     * Check if user has access to a specific kriteria
     * 
     * @param int $kriteriaId
     * @return bool
     */
    public function hasKriteriaAccess($kriteriaId)
    {
        // Admin has access to all kriteria
        if ($this->role === 'administrator') {
            return true;
        }
        
        // Non-dosen roles (koordinator, kjm, kaprodi, kajur) have access to all kriteria
        if ($this->role !== 'dosen') {
            return true;
        }
        
        // For dosen, check the kriteria_access field
        if (!empty($this->kriteria_access)) {
            return in_array($kriteriaId, $this->kriteria_access);
        }
        
        return false;
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
