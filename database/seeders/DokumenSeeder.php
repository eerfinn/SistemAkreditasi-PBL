<?php

namespace Database\Seeders;

use App\Models\Dokumen;
use App\Models\User;
use App\Models\Kriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DokumenSeeder extends Seeder
{
    public function run(): void
    {
        $userDosen1 = User::where('role', 'dosen')->first();
        $userDosen2 = User::where('role', 'dosen')->skip(1)->first();

        $kriteria1 = Kriteria::where('nama_kriteria', 'Kriteria 1')->first();
        $kriteria2 = Kriteria::where('nama_kriteria', 'Kriteria 2')->first();
        $kriteria3 = Kriteria::where('nama_kriteria', 'Kriteria 3')->first();

        $now = Carbon::now();
        $dokumenData = [];

        if ($userDosen1 && $kriteria1) {
            $dokumenData[] = [
                'user_id' => $userDosen1->id,
                'kriteria_id' => $kriteria1->id,
                'nama_dokumen' => 'RPS Matakuliah Dasar Pemrograman K1.pdf',
                'path' => 'dokumen_akreditasi/k1_rps_daspro.pdf',
                'jenis_ppepp' => Dokumen::PPEPP_PELAKSANAAN,
                'deskripsi_dokumen' => 'Rencana Pembelajaran Semester untuk matakuliah Dasar Pemrograman.',
                'status' => Dokumen::STATUS_DRAFT,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $dokumenData[] = [
                'user_id' => $userDosen1->id,
                'kriteria_id' => $kriteria1->id,
                'nama_dokumen' => 'Contoh Soal UAS K1.docx',
                'path' => 'dokumen_akreditasi/k1_soal_uas.docx',
                'jenis_ppepp' => Dokumen::PPEPP_EVALUASI,
                'deskripsi_dokumen' => 'Contoh soal Ujian Akhir Semester untuk Kriteria 1.',
                'status' => Dokumen::STATUS_DITERIMA,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(1),
            ];
        }

        if ($userDosen1 && $kriteria2) {
            $dokumenData[] = [
                'user_id' => $userDosen1->id,
                'kriteria_id' => $kriteria2->id,
                'nama_dokumen' => 'SK Tata Pamong Prodi K2.pdf',
                'path' => 'dokumen_akreditasi/k2_sk_tatapamong.pdf',
                'jenis_ppepp' => Dokumen::PPEPP_PENETAPAN,
                'deskripsi_dokumen' => 'Surat Keputusan terkait Tata Pamong Program Studi.',
                'status' => Dokumen::STATUS_REVISI,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(3),
            ];
        }

        if ($userDosen2 && $kriteria1) {
            $dokumenData[] = [
                'user_id' => $userDosen2->id,
                'kriteria_id' => $kriteria1->id,
                'nama_dokumen' => 'Modul Praktikum K1 DosenLain.pdf',
                'path' => 'dokumen_akreditasi/k1_modul_dosenlain.pdf',
                'jenis_ppepp' => Dokumen::PPEPP_PELAKSANAAN,
                'deskripsi_dokumen' => 'Modul praktikum tambahan untuk Kriteria 1 dari dosen lain.',
                'status' => Dokumen::STATUS_DRAFT,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($userDosen2 && $kriteria3) {
             $dokumenData[] = [
                'user_id' => $userDosen2->id,
                'kriteria_id' => $kriteria3->id,
                'nama_dokumen' => 'Data Prestasi Mahasiswa K3 DosenLain.xlsx',
                'path' => 'dokumen_akreditasi/k3_prestasi_mhs_dosenlain.xlsx',
                'jenis_ppepp' => Dokumen::PPEPP_PELAKSANAAN,
                'deskripsi_dokumen' => 'Rekapitulasi data prestasi mahasiswa terkait Kriteria 3.',
                'status' => Dokumen::STATUS_DIVERIFIKASI,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ];
        }

        if (!empty($dokumenData)) {
            Dokumen::insert($dokumenData);
            $this->command->info(count($dokumenData) . ' data dokumen berhasil di-seed.');
        } else {
            $this->command->info('Tidak ada user dengan peran "dosen" atau kriteria yang relevan ditemukan untuk seeding dokumen. Pastikan UserSeeder dan KriteriaSeeder sudah berjalan dengan benar.');
        }
    }
}
