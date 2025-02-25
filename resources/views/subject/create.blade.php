@extends('layouts.app')

@section('content')
<div class="row ">
    <div class="col-12 col-md-11 col-lg-11 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Mata Pelajaran</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('subjects.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            placeholder="Masukkan Mata pelajaran" value="{{ old('name') }}" autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Pelajaran</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" name="description"
                            placeholder="Masukkan Mata pelajaran">
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary mr-1" type="submit">Simpan</button>
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
