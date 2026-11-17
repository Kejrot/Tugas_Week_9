@extends('layouts.app')
@section('title','Manajemen Pengguna')
@section('content')
<div class="card">
    <h2>Manajemen Pengguna</h2>
    <a class="btn" href="{{ route('users.create') }}">+ Tambah Pengguna</a><br><br>
    <table>
        <tr><th>Nama</th><th>Email</th><th>Role</th><th></th></tr>
        @foreach($users as $u)
        <tr>
            <td>{{ $u->name }}</td><td>{{ $u->email }}</td><td><span class="badge">{{ $u->role }}</span></td>
            <td>
                @if(! $u->is(auth()->user()))
                <form method="POST" action="{{ route('users.destroy',$u) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                    @csrf @method('DELETE')<button class="btn btn-sm">Hapus</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
