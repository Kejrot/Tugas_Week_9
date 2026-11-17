<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NilaiController extends Controller
{
    /** Kelola nilai: admin & dosen. */
    public function kelola(): View
    {
        return view('nilai.kelola', [
            'nilais' => Nilai::with('mahasiswa')->latest()->get(),
            'mahasiswas' => User::where('role', 'mahasiswa')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // hanya akun ber-role mahasiswa yang boleh diberi nilai
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'mahasiswa')],
            'mata_kuliah' => ['required', 'string', 'max:255'],
            'nilai' => ['required', 'integer', 'between:0,100'],
        ]);

        Nilai::create($data);

        return redirect()->route('nilai.kelola')->with('status', 'Nilai disimpan.');
    }

    public function destroy(Nilai $nilai): RedirectResponse
    {
        $nilai->delete();

        return redirect()->route('nilai.kelola')->with('status', 'Nilai dihapus.');
    }

    /**
     * Nilai pribadi. Query selalu dibatasi ke pengguna yang login (object-level access),
     * sehingga mahasiswa tidak bisa melihat nilai milik orang lain.
     */
    public function saya(Request $request): View
    {
        return view('nilai.saya', ['nilais' => $request->user()->nilais()->latest()->get()]);
    }
}
