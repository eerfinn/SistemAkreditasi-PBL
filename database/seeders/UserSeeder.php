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
                'password' => Hash::make('admin123'),
                'role' => 'administrator',
            ],
            [
                'username' => 'dosen',
                'nama' => 'Dosen',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
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
