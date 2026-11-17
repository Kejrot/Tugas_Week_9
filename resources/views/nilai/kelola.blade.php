@extends('layouts.app')
@section('title','Kelola Nilai')
@section('content')
<div class="card">
    <h2>Kelola Nilai</h2>
    <form method="POST" action="{{ route('nilai.store') }}">
        @csrf
        <label>Mahasiswa</label>
        <select name="user_id" required>
            @foreach($mahasiswas as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
        </select>
        <label>Mata Kuliah</label><input name="mata_kuliah" required>
        <label>Nilai (0-100)</label><input type="number" name="nilai" min="0" max="100" required>
        <button class="btn">Simpan Nilai</button>
    </form>
</div>
<div class="card">
    <table>
        <tr><th>Mahasiswa</th><th>Mata Kuliah</th><th>Nilai</th><th></th></tr>
        @foreach($nilais as $n)
        <tr>
            <td>{{ $n->mahasiswa->name ?? '-' }}</td><td>{{ $n->mata_kuliah }}</td><td>{{ $n->nilai }}</td>
            <td><form method="POST" action="{{ route('nilai.destroy',$n) }}" onsubmit="return confirm('Hapus nilai?')">@csrf @method('DELETE')<button class="btn btn-sm">Hapus</button></form></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
