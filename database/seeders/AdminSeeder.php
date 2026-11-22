<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['Admin Akademik',   'admin@akademik.test',      'admin'],
            ['Dosen Pengampu',   'dosen@akademik.test',      'dosen'],
            ['Mahasiswa Satu',   'mahasiswa@akademik.test',  'mahasiswa'],
            ['Mahasiswa Dua',    'mahasiswa2@akademik.test', 'mahasiswa'],
        ];

        foreach ($akun as [$name, $email, $role]) {
            User::query()->where('email', $email)->first()
                ?? (new User)->forceFill([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'email_verified_at' => now(),
                ])->save();
        }
    }
}