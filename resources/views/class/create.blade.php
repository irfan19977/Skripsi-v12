@extends('layouts.app')

@section('content')
<div class="row ">
    <div class="col-12 col-md-11 col-lg-11 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Kelas</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('class.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>Program Studi</label>
                        <select name="prodi" class="form-control select2" required>
                            <option value="">--Pilih Program Studi--</option>
                            <option value="Akuntansi" {{ old('prodi') == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                            <option value="Teknik Komputer dan Jaringan" {{ old('prodi') == 'Teknik Komputer dan Jaringan' ? 'selected' : '' }}>Teknik Komputer dan Jaringan</option>
                            <option value="Design Komunikasi Visual" {{ old('prodi') == 'Design Komunikasi Visual' ? 'selected' : '' }}>Design Komunikasi Visual</option>
                            <option value="Asisten Keperawatan" {{ old('prodi') == 'Asisten Keperawatan' ? 'selected' : '' }}>Asisten Keperawatan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            placeholder="Masukkan Mata pelajaran" value="{{ old('name') }}" autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Grade</label>
                        <input type="number" class="form-control @error('grade') is-invalid @enderror" name="grade"
                            placeholder="Masukkan Grade" value="{{ old('grade') }}">
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary mr-1" type="submit">Simpan</button>
                        <a href="{{ route('class.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
