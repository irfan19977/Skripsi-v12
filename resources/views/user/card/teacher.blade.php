@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>User</h4>
      <div class="card-header-action">
        
        <form method="GET" action="{{ route('user.teacher') }}">
          <div class="input-group">
            <a href="{{ route('teacher.print') }}" class="btn btn-primary" data-toggle="tooltip" style="margin-right: 10px;" title="Cetak Kartu"><i class="fas fa-print"></i> Cetak Kartu Terpilih</a>
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
              <th class="text-center"><input type="checkbox" id="select-all-checkbox"></th>
              <th class="text-center">No.</th>
              <th class="text-center">Foto</th>
              <th class="text-center">NIP</th>
              <th class="text-center">Nama</th>
              <th class="text-center">Email</th>
              <th class="text-center">Alamat</th>
              <th class="text-center">QR Code</th>
              @can('users.edit')
              <th class="text-center">Status</th>
              @endcan
            </tr>
          </thead>
          <tbody>
            @foreach ($teachers as $student)
              <tr>
                <td class="text-center">
                  <input type="checkbox" name="selected_students[]" value="{{ $student->id }}" class="student-checkbox">
                </td>
                <td class="text-center">{{ ($teachers->currentPage() - 1) * $teachers->perPage() + $loop->iteration }}</td>
                <td class="text-center">
                  <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                </td>
                <td class="text-center">{{ $student->nip }}</td>
                <td>{{ ucwords(strtolower($student->name)) }}</td>
                <td>{{ ucwords(strtolower($student->email)) }}</td>
                <td>
                  @if ($student->city || $student->district || $student->village)
                    {{ $student->city ? ucwords(strtolower($student->city)) : '' }}<br>
                    {{ $student->district ? ucwords(strtolower($student->district)) : '' }}<br>
                    {{ $student->village ? ucwords(strtolower($student->village)) : '' }}
                  @else
                    <div class="text-center">-</div>
                  @endif
                </td>
                <td class="text-center"> 
                  <div style="width: 90px; height: 90px; display: inline-block;">
                  {!! $student->qr_code !!}
                  </div>
                </td>
                <td>
                    <div class="form-group">
                      <label class="custom-switch mt-2">
                        <input type="checkbox" class="custom-switch-input toggle-active" data-id="{{ $student->id }}" {{ $student->is_active ? 'checked' : '' }}>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">{{ $student->is_active ? 'Aktif' : 'Diblokir' }}</span>
                      </label>
                    </div>
               </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection

@push('script')
    {{-- BUAT MEMPERBARUI STATUS --}}
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
    {{-- PENGATURAN CHACKBOX --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Get the Select All checkbox
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    // Get all student checkboxes
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    // Get the print button
    const printButton = document.querySelector('a[title="Cetak Kartu"]');
    
    // Select All checkbox functionality
    selectAllCheckbox.addEventListener('click', function() {
        // Set all student checkboxes to the same state as the Select All checkbox
        studentCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });
    
    // Individual checkbox click handler
    studentCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('click', function() {
            // Check if all checkboxes are checked
            let allChecked = true;
            studentCheckboxes.forEach(function(cb) {
                if (!cb.checked) {
                    allChecked = false;
                }
            });
            
            // Update the Select All checkbox accordingly
            selectAllCheckbox.checked = allChecked;
        });
    });
    
    // Print button functionality
    printButton.addEventListener('click', function(event) {
        event.preventDefault();
        
        // Get all checked checkboxes
        const selectedIds = [];
        let hasSelection = false;
        
        studentCheckboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                hasSelection = true;
                selectedIds.push(checkbox.value);
            }
        });
        
        // Show error if no students selected
        if (!hasSelection) {
            swal({
                title: "Perhatian!",
                text: "Silakan pilih minimal satu siswa untuk mencetak kartu.",
                icon: "warning",
                timer: 3000,
                buttons: false
            });
            return;
        }
        
        // Create a form and submit it as POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = printButton.getAttribute('href');
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add selected students as input
        const studentsInput = document.createElement('input');
        studentsInput.type = 'hidden';
        studentsInput.name = 'students';
        studentsInput.value = selectedIds.join(',');
        form.appendChild(studentsInput);
        
        // Append to body and submit
        document.body.appendChild(form);
        form.submit();
    });
});
    </script>
    {{-- UNTUK POPUP FOTO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all student photo images
            const studentPhotos = document.querySelectorAll('td.text-center img[alt="Foto"]');
            
            // Create modal elements that will be appended to the body
            const modalContainer = document.createElement('div');
            modalContainer.className = 'photo-modal';
            modalContainer.style.cssText = `
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0,0,0,0.8);
                align-items: center;
                justify-content: center;
            `;
            
            const modalContent = document.createElement('div');
            modalContent.className = 'photo-modal-content';
            modalContent.style.cssText = `
                max-width: 50%;
                max-height: 60%;
                margin: auto;
                display: block;
                position: relative;
            `;
            
            const modalImage = document.createElement('img');
            modalImage.style.cssText = `
                display: block;
                max-width: 100%;
                max-height: 400px;
                margin: auto;
                box-shadow: 0 0 20px rgba(255,255,255,0.2);
                border-radius: 5px;
            `;
            
            const closeButton = document.createElement('span');
            closeButton.innerHTML = '&times;';
            closeButton.className = 'photo-modal-close';
            closeButton.style.cssText = `
                position: absolute;
                top: -30px;
                right: -30px;
                color: white;
                font-size: 30px;
                font-weight: bold;
                cursor: pointer;
            `;
            
            // Construct the modal elements
            modalContent.appendChild(modalImage);
            modalContent.appendChild(closeButton);
            modalContainer.appendChild(modalContent);
            document.body.appendChild(modalContainer);
            
            // Add click event to student photos
            studentPhotos.forEach(photo => {
                photo.style.cursor = 'pointer';
                
                photo.addEventListener('click', function() {
                    modalImage.src = this.src;
                    modalContainer.style.display = 'flex';
                    document.body.style.overflow = 'hidden'; // Prevent scrolling while modal is open
                });
            });
            
            // Close modal when clicking the close button
            closeButton.addEventListener('click', function() {
                modalContainer.style.display = 'none';
                document.body.style.overflow = ''; // Restore scrolling
            });
            
            // Close modal when clicking outside the image
            modalContainer.addEventListener('click', function(event) {
                if (event.target === modalContainer) {
                    modalContainer.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                }
            });
            
            // Close modal with escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modalContainer.style.display === 'flex') {
                    modalContainer.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                }
            });
        });
    </script>
@endpush