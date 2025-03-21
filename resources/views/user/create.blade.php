@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 col-md-11 col-lg-11 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah User</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                        placeholder="Masukkan Nama" value="{{ old('name') }}" autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                        placeholder="Masukkan Email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="Masukkan Password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Konfirmasi Password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>NISN</label>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror" name="nisn"
                                        placeholder="Masukkan NISN" value="{{ old('nisn') }}">
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">*Untuk siswa</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>NIP</label>
                                    <input type="text" class="form-control @error('nip') is-invalid @enderror" name="nip"
                                        placeholder="Masukkan NIP" value="{{ old('nip') }}">
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">*Untuk guru/staff</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>No Kartu</label>
                                    <input type="text" class="form-control @error('no_kartu') is-invalid @enderror" name="no_kartu"
                                        placeholder="Masukkan No Kartu" id="no_kartu" value="{{ old('no_kartu') }}" readonly>
                                    @error('no_kartu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                                        placeholder="Masukkan Nomor Telepon" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select class="form-control select2 @error('role') is-invalid @enderror" name="role">
                                        <option value="">Pilih Role</option>
                                        @foreach($roles ?? [] as $role)
                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- API-based Location Dropdowns -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select id="province" class="form-control select2 @error('province') is-invalid @enderror">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <input type="hidden" name="province" id="province_name" value="{{ old('province') }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kota/Kabupaten</label>
                                    <select id="city" class="form-control select2 @error('city') is-invalid @enderror">
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                    <input type="hidden" name="city" id="city_name" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select id="district" class="form-control select2 @error('district') is-invalid @enderror">
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <input type="hidden" name="district" id="district_name" value="{{ old('district') }}">
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Desa/Kelurahan</label>
                                    <select id="village" class="form-control select2 @error('village') is-invalid @enderror">
                                        <option value="">Pilih Desa/Kelurahan</option>
                                    </select>
                                    <input type="hidden" name="village" id="village_name" value="{{ old('village') }}">
                                    @error('village')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" name="address" 
                                placeholder="Masukkan Alamat Lengkap" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Foto</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept="image/*">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="control-label">Status Aktif</div>
                            <label class="custom-switch mt-2">
                                <input type="checkbox" name="is_active" class="custom-switch-input" checked>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">Aktif</span>
                            </label>
                        </div>

                        <div class="card-footer text-right">
                            <button class="btn btn-primary mr-1" type="submit">Simpan</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- API ALAMAT --}}
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 terlebih dahulu
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih opsi'
            });
            
            // Fetch provinces
            $.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', function(provinces) {
                $('#province').empty(); // Kosongkan dropdown terlebih dahulu
                $('#province').append(new Option('Pilih Provinsi', ''));
                
                provinces.forEach(function(province) {
                    $('#province').append(new Option(province.name, province.id));
                });
                
                // Gunakan trigger untuk memperbarui Select2
                $('#province').trigger('change');
            }).fail(function(error) {
                console.error("Error fetching provinces:", error);
            });

            // Fetch cities when province is selected
            $('#province').on('change', function() {
                var provinceId = $(this).val();
                var provinceName = $('#province option:selected').text();
                
                if (provinceId) {
                    $('#province_name').val(provinceName);
                } else {
                    $('#province_name').val('');
                }
                
                $('#city').empty().append(new Option('Pilih Kota/Kabupaten', ''));
                $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                
                // Reset hidden inputs
                $('#city_name').val('');
                $('#district_name').val('');
                $('#village_name').val('');
                
                // Trigger untuk memperbarui Select2
                $('#city, #district, #village').trigger('change');
                
                if (provinceId) {
                    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`, function(cities) {
                        cities.forEach(function(city) {
                            $('#city').append(new Option(city.name, city.id));
                        });
                        $('#city').trigger('change');
                    }).fail(function(error) {
                        console.error("Error fetching cities:", error);
                    });
                }
            });

            // Fetch districts when city is selected
            $('#city').on('change', function() {
                var cityId = $(this).val();
                var cityName = $('#city option:selected').text();
                
                if (cityId) {
                    $('#city_name').val(cityName);
                } else {
                    $('#city_name').val('');
                }
                
                $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                
                // Reset hidden inputs
                $('#district_name').val('');
                $('#village_name').val('');
                
                // Trigger untuk memperbarui Select2
                $('#district, #village').trigger('change');
                
                if (cityId) {
                    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`, function(districts) {
                        districts.forEach(function(district) {
                            $('#district').append(new Option(district.name, district.id));
                        });
                        $('#district').trigger('change');
                    }).fail(function(error) {
                        console.error("Error fetching districts:", error);
                    });
                }
            });

            // Fetch villages when district is selected
            $('#district').on('change', function() {
                var districtId = $(this).val();
                var districtName = $('#district option:selected').text();
                
                if (districtId) {
                    $('#district_name').val(districtName);
                } else {
                    $('#district_name').val('');
                }
                
                $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                
                // Reset hidden input
                $('#village_name').val('');
                
                // Trigger untuk memperbarui Select2
                $('#village').trigger('change');
                
                if (districtId) {
                    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`, function(villages) {
                        villages.forEach(function(village) {
                            $('#village').append(new Option(village.name, village.id));
                        });
                        $('#village').trigger('change');
                    }).fail(function(error) {
                        console.error("Error fetching villages:", error);
                    });
                }
            });
            
            // Save village name when selected
            $('#village').on('change', function() {
                var villageId = $(this).val();
                var villageName = $('#village option:selected').text();
                
                if (villageId) {
                    $('#village_name').val(villageName);
                } else {
                    $('#village_name').val('');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
             let lastKnownRFID = '';
             let isWaitingConfirmation = false;
             let pollingTimeout = null;
             let oldValue = $('#no_kartu').val(); // Store initial value
             
             function clearRFIDCache() {
                 return $.ajax({
                     url: '{{ route("clear.rfid") }}',
                     type: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                 });
             }
             
             function pollRFID() {
                 if (isWaitingConfirmation) {
                     pollingTimeout = setTimeout(pollRFID, 1000);
                     return;
                 }
 
                 $.ajax({
                     url: '{{ route("get.latest.rfid") }}',
                     type: 'GET',
                     success: function(response) {
                         if (response.rfid && response.rfid !== lastKnownRFID && !isWaitingConfirmation) {
                             isWaitingConfirmation = true;
                             const currentRFID = response.rfid;
                             
                             // Check if card is already in use
                             if (response.is_used) {
                                 swal({
                                     title: "Kartu Sudah Digunakan!",
                                     text: response.message,
                                     icon: "error",
                                     timer: 3000,
                                     buttons: false
                                 });
                                 
                                 clearRFIDCache().then(() => {
                                     // Reset state
                                     isWaitingConfirmation = false;
                                     $('#no_kartu').val(oldValue);
                                 });
                                 
                                 return;
                             }
                             
                             // If card is not in use, proceed with confirmation
                             swal({
                                 title: "Kartu Terdeteksi!",
                                 text: `Apakah anda akan menggunakan kartu dengan nomor ${currentRFID}?`,
                                 icon: "warning",
                                 buttons: [
                                     'Tidak',
                                     'Ya, Gunakan'
                                 ],
                                 dangerMode: true,
                             }).then(function(isConfirm) {
                                 if (isConfirm) {
                                     // Jika user memilih Ya
                                     lastKnownRFID = currentRFID;
                                     oldValue = currentRFID; // Update old value
                                     $('#no_kartu').val(currentRFID);
                                     
                                     // Tambahkan efek highlight
                                     $('#no_kartu').addClass('bg-light');
                                     setTimeout(function() {
                                         $('#no_kartu').removeClass('bg-light');
                                     }, 500);
                                     
                                     // Notifikasi sukses
                                     swal({
                                         title: "Berhasil!",
                                         text: "Nomor kartu berhasil ditambahkan",
                                         icon: "success",
                                         timer: 1500,
                                         buttons: false
                                     }).then(() => {
                                         clearRFIDCache();
                                     });
                                 } else {
                                     // Jika user memilih Tidak, kembalikan ke nilai lama
                                     $('#no_kartu').val(oldValue);
                                     
                                     // Clear the cache when user clicks No
                                     clearRFIDCache().then(() => {
                                         // Notifikasi batal
                                         swal({
                                             title: "Dibatalkan",
                                             text: "Tetap menggunakan nomor kartu sebelumnya",
                                             icon: "info",
                                             timer: 1500,
                                             buttons: false
                                         });
                                     });
                                 }
                                 // Reset waiting confirmation state
                                 isWaitingConfirmation = false;
                             });
                         }
                     },
                     complete: function() {
                         pollingTimeout = setTimeout(pollRFID, 1000);
                     }
                 });
             }
             
             // Mulai RFID polling
             pollRFID();
             
             // Reset button handler
             $('#resetRFID').click(function() {
                 $('#no_kartu').val('');
                 lastKnownRFID = '';
                 oldValue = ''; // Reset old value as well
                 isWaitingConfirmation = false;
                 
                 clearRFIDCache().then(() => {
                     swal({
                         title: "Reset!",
                         text: "Nomor kartu telah direset",
                         icon: "info",
                         timer: 1500,
                         buttons: false
                     });
                 });
             });
 
             // Cleanup when leaving page
             $(window).on('beforeunload', function() {
                 if (pollingTimeout) {
                     clearTimeout(pollingTimeout);
                 }
             });
         });
     </script>
@endpush