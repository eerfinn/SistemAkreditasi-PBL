<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarTugas extends Model
{
    use HasFactory;

    protected $table = 'daftar_tugas';
    protected $fillable = [
        'user_id', 
        'judul', 
        'deskripsi_tugas', 
        'tanggal', 
        'waktu', 
        'status',
        'show_in_calendar'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'show_in_calendar' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 