<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'nama' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'administrator',
            ],
            [
                'username' => 'dosen1',
                'nama' => 'Dosen 1',
                'email' => 'dosen1@gmail.com',
                'password' => Hash::make('dosen1123'),
                'role' => 'dosen',
                'kriteria_access' => [1, 2, 3],
            ],
            [
                'username' => 'dosen2',
                'nama' => 'Dosen 2',
                'email' => 'dosen2@gmail.com',
                'password' => Hash::make('dosen2123'),
                'role' => 'dosen',
                'kriteria_access' => [4, 5, 6],
            ],
            [
                'username' => 'dosen3',
                'nama' => 'Dosen 3',
                'email' => 'dosen3@gmail.com',
                'password' => Hash::make('dosen3123'),
                'role' => 'dosen',
                'kriteria_access' => [7, 8, 9],
            ],
            [
                'username' => 'kjm',
                'nama' => 'Kepala Jaminan Mutu',
                'email' => 'kjm@gmail.com',
                'password' => Hash::make('kjm123'),
                'role' => 'kjm',
            ],
            [
                'username' => 'kaprodi',
                'nama' => 'Ketua Program Studi',
                'email'=> 'kaprodi@gmail.com',
                'password' => Hash::make('kps123'),
                'role' => 'kaprodi',
            ],
            [
                'username' => 'kajur',
                'nama' => 'Ketua Jurusan',
                'email' => 'kajur@gmail.com',
                'password' => Hash::make('kajur123'),
                'role' => 'kajur',
            ],
            [
                'username' => 'koordinator',
                'nama' => 'Koordinator',
                'email' => 'koordinator@gmail.com',
                'password' => Hash::make('koordinator123'),
                'role' => 'koordinator',
            ],
            [
                'username' => 'direktur',
                'nama' => 'Direktur',
                'email' => 'direktur@gmail.com',
                'password' => Hash::make('direktur123'),
                'role' => 'direktur',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
