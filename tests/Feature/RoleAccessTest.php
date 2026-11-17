<?php

namespace Tests\Feature;

use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Skenario pengujian sesuai tabel "Skenario dan Hasil Pengujian" pada materi
 * + pengujian tambahan (matriks hak akses studi kasus, auth, dan keamanan data).
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function akun(string $role): User
    {
        return User::factory()->role($role)->create();
    }

    /* ---------- 7 skenario dari materi ---------- */

    public function test_admin_boleh_ke_admin(): void
    {
        $this->actingAs($this->akun('admin'))->get('/admin')->assertOk()->assertSee('Halaman Admin');
    }

    public function test_admin_boleh_ke_kelola_materi(): void
    {
        $this->actingAs($this->akun('admin'))->get('/kelola-materi')->assertOk();
    }

    public function test_dosen_boleh_ke_dosen(): void
    {
        $this->actingAs($this->akun('dosen'))->get('/dosen')->assertOk()->assertSee('Dashboard Dosen');
    }

    public function test_dosen_ditolak_ke_admin(): void
    {
        $this->actingAs($this->akun('dosen'))->get('/admin')->assertForbidden();
    }

    public function test_mahasiswa_boleh_ke_mahasiswa(): void
    {
        $this->actingAs($this->akun('mahasiswa'))->get('/mahasiswa')->assertOk()->assertSee('Dashboard Mahasiswa');
    }

    public function test_mahasiswa_ditolak_ke_kelola_materi(): void
    {
        $this->actingAs($this->akun('mahasiswa'))->get('/kelola-materi')->assertForbidden();
    }

    public function test_belum_login_ke_admin_dialihkan_ke_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    /* ---------- Matriks hak akses (studi kasus) ---------- */

    public function test_dashboard_dan_lihat_materi_untuk_semua_role(): void
    {
        foreach (['admin', 'dosen', 'mahasiswa'] as $role) {
            $u = $this->akun($role);
            $this->actingAs($u)->get('/dashboard')->assertOk();
            $this->actingAs($u)->get('/materi')->assertOk();
        }
    }

    public function test_manajemen_pengguna_hanya_admin(): void
    {
        $this->actingAs($this->akun('admin'))->get('/users')->assertOk();
        $this->actingAs($this->akun('dosen'))->get('/users')->assertForbidden();
        $this->actingAs($this->akun('mahasiswa'))->get('/users')->assertForbidden();
    }

    public function test_kelola_nilai_hanya_admin_dan_dosen(): void
    {
        $this->actingAs($this->akun('admin'))->get('/kelola-nilai')->assertOk();
        $this->actingAs($this->akun('dosen'))->get('/kelola-nilai')->assertOk();
        $this->actingAs($this->akun('mahasiswa'))->get('/kelola-nilai')->assertForbidden();
    }

    public function test_nilai_pribadi_untuk_admin_dan_mahasiswa_bukan_dosen(): void
    {
        $this->actingAs($this->akun('admin'))->get('/nilai-saya')->assertOk();
        $this->actingAs($this->akun('mahasiswa'))->get('/nilai-saya')->assertOk();
        $this->actingAs($this->akun('dosen'))->get('/nilai-saya')->assertForbidden();
    }

    public function test_aksi_tulis_juga_diproteksi(): void
    {
        $mhs = $this->akun('mahasiswa');

        $this->actingAs($mhs)->post('/kelola-materi', ['judul' => 'x'])->assertForbidden();
        $this->actingAs($mhs)->post('/users', [
            'name' => 'X', 'email' => 'x@x.test', 'password' => 'password123', 'role' => 'admin',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'x@x.test']);
    }

    public function test_tamu_ke_semua_route_terproteksi_dialihkan_ke_login(): void
    {
        foreach (['/dashboard', '/materi', '/users', '/kelola-materi', '/kelola-nilai', '/nilai-saya', '/dosen', '/mahasiswa'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    /* ---------- Fungsi & keamanan data ---------- */

    public function test_mahasiswa_hanya_melihat_nilai_miliknya(): void
    {
        $a = $this->akun('mahasiswa');
        $b = $this->akun('mahasiswa');
        Nilai::create(['user_id' => $a->id, 'mata_kuliah' => 'MK-MILIK-A', 'nilai' => 90]);
        Nilai::create(['user_id' => $b->id, 'mata_kuliah' => 'MK-MILIK-B', 'nilai' => 70]);

        $this->actingAs($a)->get('/nilai-saya')->assertSee('MK-MILIK-A')->assertDontSee('MK-MILIK-B');
    }

    public function test_dosen_dapat_menambah_materi_dan_nilai(): void
    {
        $dosen = $this->akun('dosen');
        $mhs = $this->akun('mahasiswa');

        $this->actingAs($dosen)->post('/kelola-materi', ['judul' => 'Materi Baru', 'deskripsi' => 'd'])
            ->assertRedirect(route('materi.kelola'));
        $this->assertDatabaseHas('materis', ['judul' => 'Materi Baru', 'user_id' => $dosen->id]);

        $this->actingAs($dosen)->post('/kelola-nilai', ['user_id' => $mhs->id, 'mata_kuliah' => 'PW', 'nilai' => 95])
            ->assertRedirect(route('nilai.kelola'));
        $this->assertDatabaseHas('nilais', ['user_id' => $mhs->id, 'nilai' => 95]);
    }

    public function test_nilai_tidak_bisa_diberikan_ke_akun_non_mahasiswa(): void
    {
        $dosen = $this->akun('dosen');
        $admin = $this->akun('admin');

        $this->actingAs($dosen)->post('/kelola-nilai', ['user_id' => $admin->id, 'mata_kuliah' => 'PW', 'nilai' => 80])
            ->assertSessionHasErrors('user_id');
    }

    public function test_admin_dapat_membuat_pengguna_dengan_role(): void
    {
        $this->actingAs($this->akun('admin'))->post('/users', [
            'name' => 'Dosen Baru', 'email' => 'baru@x.test', 'password' => 'password123', 'role' => 'dosen',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['email' => 'baru@x.test', 'role' => 'dosen']);
    }

    public function test_role_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->akun('admin'))->post('/users', [
            'name' => 'X', 'email' => 'x@x.test', 'password' => 'password123', 'role' => 'superuser',
        ])->assertSessionHasErrors('role');
    }

    public function test_role_tidak_dapat_diisi_lewat_mass_assignment(): void
    {
        $u = new User(['name' => 'A', 'email' => 'a@a.test', 'password' => 'password123', 'role' => 'admin']);
        $this->assertNotSame('admin', $u->role);
    }

    /* ---------- Autentikasi ---------- */

    public function test_login_berhasil_dan_logout(): void
    {
        $u = User::factory()->role('dosen')->create(['email' => 'd@x.test']); // password factory: "password"

        $this->post('/login', ['email' => 'd@x.test', 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($u);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_gagal_password_salah(): void
    {
        User::factory()->create(['email' => 'm@x.test']);

        $this->from('/login')->post('/login', ['email' => 'm@x.test', 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_pengguna_login_tidak_bisa_membuka_halaman_login(): void
    {
        $this->actingAs($this->akun('mahasiswa'))->get('/login')->assertRedirect();
    }
}
