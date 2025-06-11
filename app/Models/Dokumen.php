<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Dokumen extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_REVISI = 'revisi';
    public const STATUS_DIVERIFIKASI = 'diverifikasi';

    // Status untuk validasi bertingkat
    public const STATUS_MENUNGGU_DIREKTUR = 'menunggu_direktur';

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
        'koordinator_id',
        'direktur_id',
        'nama_dokumen',
        'jenis_ppepp',
        'path',
        'status',
        'validator_level',
        'koordinator_validated_at',
        'direktur_validated_at',
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

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function direktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'direktur_id');
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(Komen::class, 'dokumen_id');
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

    /**
     * Menentukan apakah dokumen perlu validasi direktur
     */
    public function needsDirectorValidation(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_DIREKTUR;
    }

    /**
     * Menentukan apakah dokumen sudah divalidasi sepenuhnya
     */
    public function isFullyValidated(): bool
    {
        return $this->status === self::STATUS_DIVERIFIKASI;
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

    /**
     * Scope to filter documents based on user role and status
     */
    public function scopeVisibleToUser($query, $user)
    {
        // Admin can see all documents
        if ($user->role === 'administrator') {
            return $query;
        }

        // Direktur can see ONLY documents that need their validation (after koordinator validation),
        // their rejected ones, and verified ones
        if ($user->role === 'direktur') {
            return $query->where(function($q) {
                $q->where('status', self::STATUS_MENUNGGU_DIREKTUR)
                  ->orWhere(function($q2) {
                      $q2->where('status', self::STATUS_REVISI)
                         ->where('validator_level', 'direktur');
                  })
                  ->orWhere('status', self::STATUS_DIVERIFIKASI);
            });
        }

        // Koordinator can see non-draft documents
        if ($user->role === 'koordinator') {
            return $query->where(function($q) {
                $q->where('status', '!=', self::STATUS_DRAFT);
            });
        }

        // Dosen can see all their own documents
        if ($user->role === 'dosen') {
            // Show own drafts and all other non-draft documents
            return $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('status', '!=', self::STATUS_DRAFT);
            });
        }

        // Other roles can see non-draft documents
        return $query->where('status', '!=', self::STATUS_DRAFT);
    }
}
