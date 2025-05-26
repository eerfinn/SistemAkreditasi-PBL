<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'dokumen_id',
        'kriteria_id',
        'icon',
        'color',
        'is_read',
        'link'
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the dokumen associated with the notification.
     */
    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }

    /**
     * Get the kriteria associated with the notification.
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();

        return $this;
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
