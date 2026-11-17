@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="card" style="max-width:420px;margin:3rem auto">
    <h2>Login Aplikasi Akademik</h2>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <label style="display:flex;gap:.5rem;align-items:center"><input type="checkbox" name="remember" style="width:auto;margin:0"> Ingat saya</label>
        <br><button class="btn">Masuk</button>
    </form>
</div>
@endsection
