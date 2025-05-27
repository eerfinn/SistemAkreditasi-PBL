<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'nama_kriteria',
        'deskripsi',
        'ppepp_descriptions'
    ];

    public function updatePPEPPDescription($ppepp, $description)
    {
        // Assuming you have a ppepp_descriptions column in your kriteria table
        // that stores descriptions as JSON
        $descriptions = json_decode($this->ppepp_descriptions ?? '{}', true);
        $descriptions[$ppepp] = $description;
        $this->ppepp_descriptions = json_encode($descriptions);
        $this->save();
    }
    
    /**
     * Get the comments for the kriteria
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Komen::class, 'kriteria_id');
    }
}
