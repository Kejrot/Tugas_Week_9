<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aplikasi Akademik')</title>
    <style>
        body{font-family:system-ui,sans-serif;margin:0;background:#f4f5f7;color:#1f2937}
        nav{background:#b91c1c;color:#fff;padding:.75rem 1.5rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap}
        nav a{color:#fff;text-decoration:none;font-size:.95rem}
        nav a:hover{text-decoration:underline}
        nav .spacer{flex:1}
        nav button{background:#fff;color:#b91c1c;border:0;padding:.35rem .8rem;border-radius:6px;cursor:pointer}
        main{max-width:900px;margin:1.5rem auto;padding:0 1rem}
        .card{background:#fff;border-radius:10px;padding:1.25rem;margin-bottom:1rem;box-shadow:0 1px 3px #0001}
        table{width:100%;border-collapse:collapse} th,td{padding:.5rem;border-bottom:1px solid #e5e7eb;text-align:left}
        input,select,textarea{width:100%;padding:.5rem;margin:.25rem 0 .75rem;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box}
        .btn{background:#b91c1c;color:#fff;border:0;padding:.5rem 1rem;border-radius:6px;cursor:pointer;text-decoration:none;display:inline-block}
        .btn-sm{padding:.2rem .6rem;font-size:.85rem}
        .ok{background:#dcfce7;color:#166534;padding:.6rem 1rem;border-radius:6px;margin-bottom:1rem}
        .err{background:#fee2e2;color:#991b1b;padding:.6rem 1rem;border-radius:6px;margin-bottom:1rem}
        .badge{background:#e5e7eb;border-radius:999px;padding:.1rem .6rem;font-size:.8rem}
    </style>
</head>
<body>
@auth
<nav>
    <strong>Akademik</strong>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('materi.index') }}">Lihat Materi</a>
    {{-- Menu hanya kemudahan UI. Keamanan sebenarnya ada di middleware pada route (server-side). --}}
    @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('users.index') }}">Pengguna</a>
    @endif
    @if(auth()->user()->hasRole('admin','dosen'))
        <a href="{{ route('materi.kelola') }}">Kelola Materi</a>
        <a href="{{ route('nilai.kelola') }}">Kelola Nilai</a>
    @endif
    @if(auth()->user()->hasRole('admin','mahasiswa'))
        <a href="{{ route('nilai.saya') }}">Nilai Saya</a>
    @endif
    <span class="spacer"></span>
    <span>{{ auth()->user()->name }} <span class="badge" style="color:#1f2937">{{ auth()->user()->role }}</span></span>
    <form method="POST" action="{{ route('logout') }}">@csrf<button>Logout</button></form>
</nav>
@endauth
<main>
    @if(session('status'))<div class="ok">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('content')
</main>
</body>
</html>
