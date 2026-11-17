@extends('layouts.app')
@section('title','Tambah Pengguna')
@section('content')
<div class="card">
    <h2>Tambah Pengguna</h2>
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <label>Nama</label><input name="name" value="{{ old('name') }}" required>
        <label>Email</label><input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password (min. 8 karakter)</label><input type="password" name="password" required>
        <label>Role</label>
        <select name="role" required>
            @foreach(\App\Models\User::ROLES as $r)<option value="{{ $r }}" @selected(old('role')===$r)>{{ $r }}</option>@endforeach
        </select>
        <button class="btn">Simpan</button> <a href="{{ route('users.index') }}">Batal</a>
    </form>
</div>
@endsection
