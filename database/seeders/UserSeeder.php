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
                'email' => 'erfinbrian@gmail.com',
                'password' => Hash::make('dosen1123'),
                'role' => 'dosen1',
            ],
            [
                'username' => 'dosen2',
                'nama' => 'Dosen 2',
                'password' => Hash::make('dosen2123'),
                'role' => 'dosen2',
            ],
            [
                'username' => 'dosen3',
                'nama' => 'Dosen 3',
                'password' => Hash::make('dosen3123'),
                'role' => 'dosen3',
            ],
            [
                'username' => 'kjm',
                'nama' => 'Kepala Jaminan Mutu',
                'password' => Hash::make('kjm123'),
                'role' => 'kjm',
            ],
            [
                'username' => 'kaprodi',
                'nama' => 'Ketua Program Studi',
                'password' => Hash::make('kps123'),
                'role' => 'kaprodi',
            ],
            [
                'username' => 'kajur',
                'nama' => 'Ketua Jurusan',
                'password' => Hash::make('kajur123'),
                'role' => 'kajur',
            ],
            [
                'username' => 'koordinator',
                'nama' => 'Koordinator',
                'password' => Hash::make('koordinator123'),
                'role' => 'koordinator',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
