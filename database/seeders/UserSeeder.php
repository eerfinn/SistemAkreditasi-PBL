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
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'level_id' => 1, // ADM
            ],
            [
                'username' => 'anggota',
                'name' => 'Dosen',
                'email' => 'dosen1@example.com',
                'password' => Hash::make('anggota123'),
                'level_id' => 2, // ANG
            ],
            [
                'username' => 'kjm',
                'name' => 'Kepala Jaminan Mutu',
                'email' => 'kjm@example.com',
                'password' => Hash::make('kjm123'),
                'level_id' => 3, // KJM
            ],
            [
                'username' => 'kaprodi',
                'name' => 'Ketua Program Studi',
                'email' => 'kaprodi@example.com',
                'password' => Hash::make('kps123'),
                'level_id' => 4, // KPS
            ],
            [
                'username' => 'kajur',
                'name' => 'Ketua Jurusan',
                'email' => 'kajur@example.com',
                'password' => Hash::make('kajur123'),
                'level_id' => 5, // KJR
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
