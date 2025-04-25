<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'level_kode' => 'ADM',
                'level_nama' => 'Administrator',
            ],
            [
                'level_kode' => 'ANG',
                'level_nama' => 'Anggota',
            ],
            [
                'level_kode' => 'KRT',
                'level_nama' => 'Koordinator Kriteria',
            ],
            [
                'level_kode' => 'KJM',
                'level_nama' => 'Kantor Jaminan Mutu',
            ],
            [
                'level_kode' => 'KPS',
                'level_nama' => 'Ketua Program Studi',
            ],
            [
                'level_kode' => 'KJR',
                'level_nama' => 'Ketua Jurusan',
            ]    
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
} 