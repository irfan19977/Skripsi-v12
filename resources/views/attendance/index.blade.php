@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @livewire('attendance-table', ['classes' => $classes, 'subjects' => $subjects])
</div>

<!-- QR Modal (Tetap dipertahankan dari kode asli) -->
<div class="modal fade" id="qrScanModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">Scan QR Code Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <video id="video" width="100%" height="300" style="border: 1px solid #ccc;"></video>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.min.js"></script>
    <script>
        // Kode JS untuk scanner QR (dipertahankan dari kode asli)
        const video = document.getElementById('video');
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
    
        // Fungsi untuk memulai kamera
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(stream => {
                    video.srcObject = stream;
                    video.play();
                    requestAnimationFrame(scanQR);
                })
                .catch(err => {
                    console.error("Error accessing the camera: ", err);
                    alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
                });
        }
    
        function scanQR() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    // Stop video stream
                    const stream = video.srcObject;
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                    video.srcObject = null;
                    
                    // Extract NISN from QR code
                    const nisn = extractNISN(code.data);
                    
                    if (nisn) {
                        sendAttendance(nisn);
                    } else {
                        alert('QR Code tidak valid');
                        $('#qrScanModal').modal('hide');
                    }
                } else {
                    requestAnimationFrame(scanQR);
                }
            } else {
                requestAnimationFrame(scanQR);
            }
        }
    
        function extractNISN(qrData) {
            // Assuming QR code is in format "NISN: 1234567890"
            const match = qrData.match(/NISN:\s*(\d+)/);
            return match ? match[1] : null;
        }
    
        function sendAttendance(nisn) {
            fetch('{{ route('attendances.scan-qr') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nisn: nisn })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Absensi Berhasil!",
                        text: `${data.student.name} - ${data.student.class}\nMata Pelajaran: ${data.student.subject}\nPengajar: ${data.student.teacher}`,
                        icon: "success",
                        timer: 3000,
                        buttons: false
                    }).then(() => {
                        // Reload data with Livewire instead of reloading page
                        Livewire.emit('refresh');
                    });
                } else {
                    swal({
                        title: "Absensi Gagal",
                        text: data.message,
                        icon: "error",
                        timer: 3000,
                        buttons: false
                    });
                }
                $('#qrScanModal').modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                swal({
                    title: "Kesalahan",
                    text: "Terjadi kesalahan saat melakukan absensi",
                    icon: "error",
                    timer: 3000,
                    buttons: false
                });
                $('#qrScanModal').modal('hide');
            });
        }
    
        // Event listener untuk membuka modal
        $('#qrScanModal').on('show.bs.modal', function () {
            startCamera();
        });
    
        // Event listener untuk menutup modal
        $('#qrScanModal').on('hidden.bs.modal', function () {
            const stream = video.srcObject;
            if (stream) {
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
            }
            video.srcObject = null;
        });
    </script>
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
                    const formElement = document.querySelector(`#delete-form-${id}`);
                        if (formElement) {
                        const rowToRemove = formElement.closest('tr');
                        if (rowToRemove) {
                            rowToRemove.remove();
                            
                            // Perbarui nomor urut
                            renumberTableRows();
                        }
                    }
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
@endpush