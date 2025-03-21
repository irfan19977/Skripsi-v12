@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Daftar Kelas</h4>
      <div class="card-header-action">
        
        <form method="GET" action="{{ route('class.index') }}">
          <div class="input-group">
            @can('class.create')
                <a href="{{ route('class.create') }}" class="btn btn-primary" data-toggle="tooltip" style="margin-right: 10px;" title="Tambah Data"><i class="fas fa-plus"></i></a>
            @endcan
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
              <th class="text-center">Program Studi</th>
              <th class="text-center">Nama Kelas</th>
              <th class="text-center">Grade</th>
              <th class="text-center">Total Siswa</th>
              @can('class.edit')
                <th class="text-center">Aksi</th>
              @endcan
            </tr>
          </thead>
          <tbody>
            @foreach ($classes as $class)
              <tr>
                <td class="text-center">{{ ($classes->currentPage() - 1) * $classes->perPage() + $loop->iteration }}</td>
                <td>{{ $class->prodi }}</td>
                <td>{{ $class->name }}</td>
                <td class="text-center">{{ $class->grade }}</td>
                <td class="text-center">{{ $class->student_count }}</td>
                @can('class.edit')
                  <td class="text-center">
                    <a href="{{ route('class.edit-assign', $class->id) }}" 
                        class="btn btn-primary btn-action mr-1" 
                        data-toggle="tooltip" 
                        title="Tambah Siswa">
                        <i class="fa fa-door-open"></i>
                     </a>

                    <a href="{{ route('class.edit', $class->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="Edit">
                      <i class="fas fa-pencil-alt"></i>
                    </a>

                    <form id="delete-form-{{ $class->id }}" action="{{ route('class.destroy', $class->id) }}" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" onclick="confirmDelete('{{ $class->id }}')">
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
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
              _method: 'DELETE'
            })
          })
          .then(response => {
            if (!response.ok && !response.headers.get('content-type')?.includes('application/json')) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              swal({
                title: "Berhasil!",
                text: data.message || "Data berhasil dihapus.",
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
              // Show the specific error message from the server
              swal("Gagal", data.message || "Terjadi kesalahan saat menghapus data.", "error");
            }
          })
          .catch(error => {
            console.error('Error:', error);
            swal("Error", "Terjadi kesalahan pada server.", "error");
          });
        }
      });
    }

    function renumberTableRows() {
      const tableBody = document.querySelector('#sortable-table tbody');
      const rows = tableBody.querySelectorAll('tr');
      
      // Get current page and per page values
      const currentPage = {{ $classes->currentPage() }};
      const perPage = {{ $classes->perPage() }};
      
      rows.forEach((row, index) => {
        const numberCell = row.querySelector('td:first-child');
        if (numberCell) {
          numberCell.textContent = (currentPage - 1) * perPage + index + 1;
        }
      });
    }
  </script>
  
@endsection

