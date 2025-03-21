@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Data Siswa Kelas {{ $class->grade }} {{ $class->prodi }} </h4>
        <span>Tahun Akademik ({{ date('Y') }}/{{ date('Y') + 1 }})</span>
    </div>
    <div class="card-body">
        <form action="{{ route('class.update-assign', $class->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Siswa yang sudah terdaftar -->
                <div class="col-md-6 mb-4">
                    <h5>Siswa yang Sudah Terdaftar</h5>
                    <input type="text" id="search-assigned" class="form-control mb-3" placeholder="Cari siswa terdaftar...">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="check-all-assigned">
                                    </th>
                                    <th class="text-center">NISN</th>
                                    <th class="text-center">NAMA</th>
                                </tr>
                            </thead>
                            <tbody id="assigned-table-body">
                                @forelse($assignedStudents as $student)
                                <tr class="assigned-row">
                                    <td>
                                        <input type="checkbox" name="remove_student_ids[]" 
                                               value="{{ $student->student->id }}" 
                                               class="assigned-checkbox">
                                    </td>
                                    <td class="text-center">{{ $student->student->nisn }}</td>
                                    <td>{{ ucwords(strtolower($student->student->name)) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada siswa terdaftar</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <small>*Centang jika ingin mengeluarkan siswa</small>
                    </div>
                </div>

                <!-- Tambah siswa baru -->
                <div class="col-md-6">
                    <h5>Tambah Siswa Baru</h5>
                    <input type="text" id="search-available" class="form-control mb-3" placeholder="Cari siswa baru...">
                    <div class="selectgroup selectgroup-pills" id="available-students">
                        @forelse($availableStudents as $student)
                        <label class="selectgroup-item available-row mb-2">
                            <input type="checkbox" name="student_ids[]" 
                                   value="{{ $student->id }}" class="selectgroup-input">
                            <span class="selectgroup-button">
                                {{ $student->nisn }} - {{ ucwords(strtolower($student->name)) }}
                            </span>
                        </label>
                        @empty
                        <div class="alert alert-info text-center">
                            Tidak ada siswa yang dapat ditambahkan.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <button class="btn btn-primary mr-1" type="submit">Perbarui Daftar Siswa</button>
                <a href="{{ route('class.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script>

// Pencarian siswa yang sudah terdaftar
document.getElementById('search-assigned').addEventListener('input', function() {
    let searchValue = this.value.toLowerCase();
    document.querySelectorAll('.assigned-row').forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Pencarian siswa baru
document.getElementById('search-available').addEventListener('input', function() {
    let searchValue = this.value.toLowerCase();
    document.querySelectorAll('.available-row').forEach(label => {
        let text = label.textContent.toLowerCase();
        label.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Check all functionality
document.getElementById('check-all-assigned').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.assigned-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endpush
