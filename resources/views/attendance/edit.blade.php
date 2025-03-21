@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Catatan Kehadiran</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('attendances.update', $attendances->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">NISN</label>
                <div class="col-sm-12 col-md-7">
                    <div class="input-group">
                        <input type="text" name="nisn" id="nisn" class="form-control" 
                               value="{{ $attendances->student->nisn }}"
                               placeholder="Masukkan NISN Siswa" required readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="findStudentBtn" disabled>Cari Siswa</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Siswa</label>
                <div class="col-sm-12 col-md-7">
                    <input type="text" class="form-control" value="{{ $attendances->student->name }}" readonly>
                </div>
            </div>            

            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mata Pelajaran</label>
                <div class="col-sm-12 col-md-7">
                    <input type="text" class="form-control" value="{{ $attendances->subject->name }}" readonly>
                </div>
            </div>
            
            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Guru</label>
                <div class="col-sm-12 col-md-7">
                    <input type="text" class="form-control" value="{{ $attendances->teacher->name }}" readonly>
                </div>
            </div>
            
            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tanggal</label>
                <div class="col-sm-12 col-md-7">
                    <input type="date" class="form-control" value="{{ $attendances->date }}" readonly>
                </div>
            </div>
            
            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Jam</label>
                <div class="col-sm-12 col-md-7">
                    <input type="time" class="form-control" value="{{ $attendances->time }}" readonly>
                </div>
            </div>            

            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status</label>
                <div class="col-sm-12 col-md-7">
                    <select name="status" class="form-control select2" required>
                        <option value="hadir" {{ isset($attendances) && $attendances->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="Izin" {{ isset($attendances) && $attendances->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Sakit" {{ isset($attendances) && $attendances->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Alpha" {{ isset($attendances) && $attendances->status == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
            </div>

            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Catatan</label>
                <div class="col-sm-12 col-md-7">
                    <select name="notes" class="form-control selectric" required>
                        <option value="kartu hilang" {{ $attendances->notes == 'kartu hilang' ? 'selected' : '' }}>Kartu Hilang</option>
                        <option value="kartu tertinggal" {{ $attendances->notes == 'kartu tertinggal' ? 'selected' : '' }}>Kartu Tertinggal</option>
                    </select>
                </div>
            </div>

            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                <div class="col-sm-12 col-md-7">
                    <button type="submit" class="btn btn-primary">Update Catatan Kehadiran</button>
                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary ml-2">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#findStudentBtn').on('click', function() {
            const nisn = $('#nisn').val().trim();
            
            if (!nisn) {
                swal({
                    title: "Peringatan",
                    text: "Silakan masukkan NISN",
                    icon: "warning",
                    timer: 3000,
                    button: false
                });
                return;
            }

            $.ajax({
                url: `/students/find-by-nisn/${nisn}`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    // Populate student select
                    $('#studentSelect').html(`
                        <option value="${data.id}" selected>
                            ${data.name} (${data.class_name})
                        </option>
                    `);
                    $('#studentSelect').trigger('change');

                    // Automatically populate subject and refresh Select2
                    $('select[name="subject_id"]').val(data.subject_id).trigger('change');

                    // Automatically populate teacher and refresh Select2
                    $('select[name="teacher_id"]').val(data.teacher_id).trigger('change');

                    // Show success message
                    swal({
                        title: "Berhasil",
                        text: `Siswa ditemukan: ${data.name}\nMapel: ${data.subject_name}\nGuru: ${data.teacher_name}`,
                        icon: "success",
                        timer: 3000,
                        button: false
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    
                    // Show error message using SweetAlert
                    swal({
                        title: "Kesalahan",
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'Siswa tidak ditemukan',
                        icon: "error",
                        timer: 3000,
                        button: false
                    });
                }
            });
        });
    });
</script>
@endsection