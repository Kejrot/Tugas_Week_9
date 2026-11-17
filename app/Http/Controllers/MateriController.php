<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriController extends Controller
{
    /** Lihat materi: semua role yang login. */
    public function index(): View
    {
        return view('materi.index', ['materis' => Materi::with('pembuat')->latest()->get()]);
    }

    /** Kelola materi: admin & dosen. */
    public function kelola(): View
    {
        return view('materi.kelola', ['materis' => Materi::with('pembuat')->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Materi::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('materi.kelola')->with('status', 'Materi ditambahkan.');
    }

    public function destroy(Materi $materi): RedirectResponse
    {
        $materi->delete();

        return redirect()->route('materi.kelola')->with('status', 'Materi dihapus.');
    }
}
