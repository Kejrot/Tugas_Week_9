@extends('layouts.app')
@section('title','Materi')
@section('content')
<div class="card">
    <h2>Daftar Materi</h2>
    @forelse($materis as $m)
        <div style="border-bottom:1px solid #e5e7eb;padding:.6rem 0">
            <strong>{{ $m->judul }}</strong> <small>oleh {{ $m->pembuat->name ?? '-' }}</small>
            <div>{{ $m->deskripsi }}</div>
        </div>
    @empty <p>Belum ada materi.</p> @endforelse
</div>
@endsection
