@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="card">
    <h2>Dashboard</h2>
    <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong> (role: <strong>{{ auth()->user()->role }}</strong>).</p>
    <p>Pengguna: {{ $jumlahUser }} &middot; Materi: {{ $jumlahMateri }} &middot; Data nilai: {{ $jumlahNilai }}</p>
</div>
@endsection
