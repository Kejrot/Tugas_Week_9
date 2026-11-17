<?php

namespace Database\Seeders;

use App\Models\Materi;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['Admin Akademik',   'admin@akademik.test',     'admin'],
            ['Dosen Pengampu',   'dosen@akademik.test',     'dosen'],
            ['Mahasiswa Satu',   'mahasiswa@akademik.test', 'mahasiswa'],
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

        $dosen = User::where('email', 'dosen@akademik.test')->first();
        $m1 = User::where('email', 'mahasiswa@akademik.test')->first();
        $m2 = User::where('email', 'mahasiswa2@akademik.test')->first();

        if (Materi::count() === 0) {
            Materi::create(['judul' => 'Pengantar Middleware', 'deskripsi' => 'Konsep dan alur kerja middleware di Laravel.', 'user_id' => $dosen->id]);
            Materi::create(['judul' => 'RBAC & Least Privilege', 'deskripsi' => 'Pembatasan hak akses berdasarkan role.', 'user_id' => $dosen->id]);
        }

        if (Nilai::count() === 0) {
            Nilai::create(['user_id' => $m1->id, 'mata_kuliah' => 'Pemrograman Web', 'nilai' => 88]);
            Nilai::create(['user_id' => $m1->id, 'mata_kuliah' => 'Basis Data', 'nilai' => 90]);
            Nilai::create(['user_id' => $m2->id, 'mata_kuliah' => 'Pemrograman Web', 'nilai' => 75]);
        }
    }
}
