@extends('layouts.app')
@section('title','Kelola Materi')
@section('content')
<div class="card">
    <h2>Kelola Materi</h2>
    <form method="POST" action="{{ route('materi.store') }}">
        @csrf
        <label>Judul</label><input name="judul" required>
        <label>Deskripsi</label><textarea name="deskripsi" rows="2"></textarea>
        <button class="btn">Tambah Materi</button>
    </form>
</div>
<div class="card">
    <table>
        <tr><th>Judul</th><th>Pembuat</th><th></th></tr>
        @foreach($materis as $m)
        <tr>
            <td>{{ $m->judul }}</td><td>{{ $m->pembuat->name ?? '-' }}</td>
            <td><form method="POST" action="{{ route('materi.destroy',$m) }}" onsubmit="return confirm('Hapus materi?')">@csrf @method('DELETE')<button class="btn btn-sm">Hapus</button></form></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
