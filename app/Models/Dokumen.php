<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        'kriteria_id',
        'user_id',
        'validator_id',
        'nama_dokumen',
        'jenis_ppepp',
        'path',
        'status',
        'komentar',
        'validated_at',
        'is_admin_upload',
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
            // Check if the file exists in storage
            if (Storage::disk('public')->exists($this->path)) {
                // Generate URL manually
                Log::info('File exists in storage', [
                    'dokumen_id' => $this->id,
                    'path' => $this->path
                ]);
                
                return asset('storage/' . $this->path);
            } else {
                Log::warning('File not found in storage', [
                    'dokumen_id' => $this->id,
                    'path' => $this->path
                ]);
                return null;
            }
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
