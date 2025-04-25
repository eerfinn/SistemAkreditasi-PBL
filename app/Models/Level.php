<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'level_id';
    
    protected $fillable = [
        'level_kode',
        'name',
        'description',
        'status'
    ];
    
    public function users()
    {
        return $this->hasMany(User::class, 'level_id');
    }
}
