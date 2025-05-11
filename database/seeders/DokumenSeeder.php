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
    /**
     * Jalankan proses seeding database.
     */
    public function run(): void
    {
        // Pastikan UserSeeder dan KriteriaSeeder sudah dijalankan sebelumnya
        // Ambil contoh user dan kriteria yang sudah ada
        $userDosen1 = User::where('role', 'dosen')->first(); // Ambil user dosen pertama
        $userDosen2 = User::where('role', 'dosen')->skip(1)->first(); // Ambil user dosen kedua (jika ada)

        // Mengambil objek Kriteria berdasarkan nama_kriteria yang didefinisikan di KriteriaSeeder
        $kriteria1 = Kriteria::where('nama_kriteria', 'Kriteria 1')->first();
        $kriteria2 = Kriteria::where('nama_kriteria', 'Kriteria 2')->first();
        $kriteria3 = Kriteria::where('nama_kriteria', 'Kriteria 3')->first();
        // Anda bisa menambahkan pengambilan untuk kriteria lainnya jika diperlukan

        $now = Carbon::now(); // Waktu referensi awal

        $dokumenData = [];

        // Hanya buat data jika user dosen dan kriteria yang relevan ditemukan
        if ($userDosen1 && $kriteria1) {
            $dokumenData[] = [
                'user_id' => $userDosen1->id,
                'kriteria_id' => $kriteria1->id,
                'nama_dokumen' => 'RPS Matakuliah Dasar Pemrograman K1.pdf',
                'path' => 'dokumen_akreditasi/k1_rps_daspro.pdf', // Contoh path
                'status' => 'menunggu',
                'created_at' => $now, // Menggunakan waktu referensi awal
                'updated_at' => $now, // Menggunakan waktu referensi awal
            ];
            $dokumenData[] = [
                'user_id' => $userDosen1->id,
                'kriteria_id' => $kriteria1->id,
                'nama_dokumen' => 'Contoh Soal UAS K1.docx',
                'path' => 'dokumen_akreditasi/k1_soal_uas.docx',
                'status' => 'diterima',
                'created_at' => $now->copy()->subDays(2), // Gunakan copy() agar $now asli tidak berubah
                'updated_at' => $now->copy()->subDays(1), // Gunakan copy()
            ];
        }

        if ($userDosen1 && $kriteria2) {
            $dokumenData[] = [
                'user_id' => $userDosen1->id,
                'kriteria_id' => $kriteria2->id,
                'nama_dokumen' => 'SK Tata Pamong Prodi K2.pdf', // Nama file disesuaikan agar lebih jelas
                'path' => 'dokumen_akreditasi/k2_sk_tatapamong.pdf',
                'status' => 'revisi',
                'created_at' => $now->copy()->subDays(5), // Gunakan copy()
                'updated_at' => $now->copy()->subDays(3), // Gunakan copy()
            ];
        }

        // Menggunakan $userDosen2 yang benar
        if ($userDosen2 && $kriteria1) {
            $dokumenData[] = [
                'user_id' => $userDosen2->id,
                'kriteria_id' => $kriteria1->id,
                'nama_dokumen' => 'Modul Praktikum K1 DosenLain.pdf', // Nama file disesuaikan
                'path' => 'dokumen_akreditasi/k1_modul_dosenlain.pdf',
                'status' => 'menunggu',
                'created_at' => $now, // Menggunakan waktu referensi awal
                'updated_at' => $now, // Menggunakan waktu referensi awal
            ];
        }

        // Menggunakan $userDosen2 yang benar
        if ($userDosen2 && $kriteria3) {
             $dokumenData[] = [
                'user_id' => $userDosen2->id,
                'kriteria_id' => $kriteria3->id,
                'nama_dokumen' => 'Data Prestasi Mahasiswa K3 DosenLain.xlsx', // Nama file disesuaikan
                'path' => 'dokumen_akreditasi/k3_prestasi_mhs_dosenlain.xlsx',
                'status' => 'diverifikasi',
                'created_at' => $now->copy()->subDays(10), // Gunakan copy()
                'updated_at' => $now->copy()->subDays(10), // Gunakan copy()
            ];
        }

        // Opsional: Hapus data lama dari tabel dokumen sebelum seeding jika diperlukan
        // Dokumen::query()->delete();

        if (!empty($dokumenData)) {
            Dokumen::insert($dokumenData); // Menggunakan insert untuk efisiensi saat memasukkan banyak data
            $this->command->info(count($dokumenData) . ' data dokumen berhasil di-seed.');
        } else {
            // Pesan disesuaikan untuk mencerminkan pencarian user 'dosen'
            $this->command->info('Tidak ada user dengan peran "dosen" atau kriteria yang relevan ditemukan untuk seeding dokumen. Pastikan UserSeeder dan KriteriaSeeder sudah berjalan dengan benar.');
        }
    }
}
