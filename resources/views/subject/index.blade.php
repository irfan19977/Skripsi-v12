@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Mata Pelajaran</h4>
      <div class="card-header-action">
        
        <form method="GET" action="{{ route('subjects.index') }}">
          <div class="input-group">
            <a href="{{ route('subjects.create') }}" class="btn btn-primary" data-toggle="tooltip" style="margin-right: 10px;" title="Tambah Data"><i class="fas fa-plus"></i></a>
            <input type="text" class="form-control" placeholder="Search" name="q">
            <div class="input-group-btn">
              <button class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped" id="sortable-table">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th>Kode Mata Pelajaran</th>
              <th>Mata Pelajaran</th>
              <th>Deskripsi</th>
              @can('subjects.edit')
                <th>Aksi</th>
              @endcan
            </tr>
          </thead>
          <tbody>
            @foreach ($subjects as $subject)
              <tr>
                <td class="text-center">{{ ($subjects->currentPage() - 1) * $subjects->perPage() + $loop->iteration }}</td>
                <td>{{ $subject->code }}</td>
                <td>{{ $subject->name }}</td>
                <td>{{ $subject->description }}</td>
                @can('subjects.edit')
                  <td>
                    <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="Edit">
                      <i class="fas fa-pencil-alt"></i>
                    </a>

                    <form id="delete-form-{{ $subject->id }}" action="{{ route('subjects.destroy', $subject->id) }}" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" onclick="confirmDelete('{{ $subject->id }}')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                    
                  </td>
                @endcan
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    
  </div>
  
  <script>
    function confirmDelete(id) {
      swal({
        title: "Apakah Anda Yakin?",
        text: "Data ini akan dihapus secara permanen!",
        icon: "warning",
        buttons: [
          'Tidak',
          'Ya, Hapus'
        ],
        dangerMode: true,
      }).then(function(isConfirm) {
        if (isConfirm) {
          const form = document.getElementById(`delete-form-${id}`);
          const url = form.action;

          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              _method: 'DELETE'
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              swal({
                title: "Berhasil!",
                text: "Data berhasil dihapus.",
                icon: "success",
                timer: 3000,
                buttons: false
              }).then(() => {
                // Hapus baris tabel
                const rowToRemove = document.querySelector(`#delete-form-${id}`).closest('tr');
                rowToRemove.remove();

                // Perbarui nomor urut
                renumberTableRows();
              });
            } else {
              swal("Gagal", "Terjadi kesalahan saat menghapus data.", "error");
            }
          })
          .catch(error => {
            console.error("Error:", error);
            swal("Gagal", "Terjadi kesalahan saat menghapus data.", "error");
          });
        }
      });
    }

    
  
  </script>
  
@endsection

