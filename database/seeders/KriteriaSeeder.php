<?php

namespace Database\Seeders;

use App\Models\Kriteria; // Menggunakan model Kriteria Anda
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon; // Untuk mengisi timestamp jika diperlukan (meskipun create() otomatis)

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteria = [
            [
                'nama_kriteria' => 'Kriteria 1',
                'deskripsi' => 'Visi, Misi, Tujuan, dan Strategi',
            ],
            [
                'nama_kriteria' => 'Kriteria 2',
                'deskripsi' => 'Tata Pamong, Tata Kelola, dan Kerjasama',
            ],
            [
                'nama_kriteria' => 'Kriteria 3',
                'deskripsi' => 'Mahasiswa',
            ],
            [
                'nama_kriteria' => 'Kriteria 4',
                'deskripsi' => 'Sumber Daya Manusia',
            ],
            [
                'nama_kriteria' => 'Kriteria 5',
                'deskripsi' => 'Keuangan, Sarana, dan Prasarana',
            ],
            [
                'nama_kriteria' => 'Kriteria 6',
                'deskripsi' => 'Pendidikan',
            ],
            [
                'nama_kriteria' => 'Kriteria 7',
                'deskripsi' => 'Penelitian',
            ],
            [
                'nama_kriteria' => 'Kriteria 8',
                'deskripsi' => 'Pengabdian kepada Masyarakat',
            ],
            [
                'nama_kriteria' => 'Kriteria 9',
                'deskripsi' => 'Luaran dan Capaian Tridarma',
            ],
        ];

        $now = Carbon::now();

        foreach ($kriteria as $kriteria) {
            Kriteria::create($kriteria);
        }
    }
}
