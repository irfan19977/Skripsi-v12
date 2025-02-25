@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>User</h4>
      <div class="card-header-action">
        
        <form method="GET" action="{{ route('users.index') }}">
          <div class="input-group">
            <a href="{{ route('users.create') }}" class="btn btn-primary" data-toggle="tooltip" style="margin-right: 10px;" title="Tambah Data"><i class="fas fa-plus"></i></a>
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
              <th class="text-center">Nama User</th>
              <th class="text-center">Email</th>
              <th class="text-center">Role</th>
              <th class="text-center">QR Code</th>
              <th class="text-center">Status</th>
              @can('users.edit')
              <th class="text-center">Aksi</th>
              @endcan
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td class="text-center">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $role)
                        <div class="badge badge-success mb-1 mr-1 mt-1">{{ $role }}</div>
                        @endforeach
                    @endif
                </td>
                <td> 
                  <div style="width: 90px; height: 90px; display: inline-block;">
                  {!! $user->qr_code !!}
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <label class="custom-switch mt-2">
                      <input type="checkbox" class="custom-switch-input toggle-active" data-id="{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }}>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">{{ $user->is_active ? 'Aktif' : 'Diblokir' }}</span>
                    </label>
                  </div>
                </td>
                @can('users.edit')
                  <td class="text-center">
                    
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="Edit">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="Lihat Kartu">
                      <i class="fas fa-eye"></i>
                    </a>

                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" onclick="confirmDelete('{{ $user->id }}')">
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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const toggleSwitches = document.querySelectorAll('.toggle-active');
        
        toggleSwitches.forEach(switchElement => {
            switchElement.addEventListener('change', function() {
                const userId = this.dataset.id;
                const isChecked = this.checked;
                const descriptionElement = this.closest('.custom-switch').querySelector('.custom-switch-description');
                
                // Create form data
                const formData = new FormData();
                formData.append('_token', csrfToken);
                
                // Show loading indicator
                descriptionElement.textContent = 'Memperbarui...';
                
                fetch(`/users/${userId}/toggle-active`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update UI
                        descriptionElement.textContent = data.is_active ? 'Aktif' : 'Diblokir';
                        
                        swal({
                            title: "Berhasil!",
                            text: data.message,
                            icon: "success",
                            timer: 3000,
                            buttons: false
                        });
                    } else {
                        // Restore original state
                        this.checked = !isChecked;
                        descriptionElement.textContent = isChecked ? 'Diblokir' : 'Aktif';
                        
                        swal("Gagal", "Gagal memperbarui status", "error");
                    }
                })
                .catch(error => {
                    // Restore original state
                    this.checked = !isChecked;
                    descriptionElement.textContent = isChecked ? 'Diblokir' : 'Aktif';
                    
                    console.error('Error:', error);
                    swal("Error", "Terjadi kesalahan pada server", "error");
                });
            });
        });
    });
  </script>
  
@endsection

