<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'content',
        'kriteria_id',
        'ppepp_type',
        'created_by',
    ];

    /**
     * Get the kriteria that owns the template
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Get the user that created the template
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
