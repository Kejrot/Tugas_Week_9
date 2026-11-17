<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'jumlahUser' => User::count(),
            'jumlahMateri' => Materi::count(),
            'jumlahNilai' => Nilai::count(),
        ]);
    }
}
