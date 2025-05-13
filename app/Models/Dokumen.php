<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Dokumen extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_REVISI = 'revisi';
    public const STATUS_DITERIMA = 'diterima';
    public const STATUS_DIVERIFIKASI = 'diverifikasi';

    public const PPEPP_PENETAPAN = 'penetapan';
    public const PPEPP_PELAKSANAAN = 'pelaksanaan';
    public const PPEPP_EVALUASI = 'evaluasi';
    public const PPEPP_PENGENDALIAN = 'pengendalian';
    public const PPEPP_PENINGKATAN = 'peningkatan';

    protected $table = 'dokumen';

    protected $fillable = [
        'user_id',
        'kriteria_id',
        'nama_dokumen',
        'path',
        'jenis_ppepp',
        'deskripsi_dokumen',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->path) {
            return Storage::disk('public')->url($this->path);
        }
        return null;
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($dokumen) {
            if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
                Storage::disk('public')->delete($dokumen->path);
            }
        });
    }
}
