@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 col-md-11 col-lg-11 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Edit User</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                        placeholder="Masukkan Nama" value="{{ old('name', $user->name) }}" autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                        placeholder="Masukkan Email" value="{{ old('email', $user->email) }}">
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
                                    <small class="text-muted d-block">*Kosongkan jika tidak ingin mengubah password</small>
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
                                        placeholder="Masukkan NISN" value="{{ old('nisn', $user->nisn) }}">
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
                                        placeholder="Masukkan NIP" value="{{ old('nip', $user->nip) }}">
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
                                        placeholder="Masukkan No Kartu" id="no_kartu" value="{{ old('no_kartu', $user->no_kartu) }}">
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
                                        placeholder="Masukkan Nomor Telepon" value="{{ old('phone', $user->phone) }}">
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
                                            <option value="{{ $role->name }}" {{ (old('role', $userRole) == $role->name) ? 'selected' : '' }}>
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
                                    <select id="province" class="form-control select2 @error('province') is-invalid @enderror" name="province">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <input type="hidden" name="province_name" id="province_name" value="{{ $user->province }}">
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
                                    <input type="hidden" name="city_name" id="city_name" value="{{ $user->city }}">
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
                                    <input type="hidden" name="district_name" id="district_name" value="{{ $user->district }}">
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
                                    <input type="hidden" name="village_name" id="village_name" value="{{ $user->village }}">
                                    @error('village')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" name="address" 
                                placeholder="Masukkan Alamat Lengkap" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Foto</label>
                            @if($user->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept="image/*">
                            <small class="text-muted">*Kosongkan jika tidak ingin mengubah foto</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="control-label">Status Aktif</div>
                            <label class="custom-switch mt-2">
                                <input type="checkbox" name="is_active" class="custom-switch-input" {{ $user->is_active ? 'checked' : '' }}>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">Aktif</span>
                            </label>
                        </div>

                        <div class="card-footer text-right">
                            <button class="btn btn-primary mr-1" type="submit">Simpan Perubahan</button>
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
            var userProvince = "{{ $user->province }}";
            var userCity = "{{ $user->city }}";
            var userDistrict = "{{ $user->district }}";
            var userVillage = "{{ $user->village }}";
            var provinceId, cityId, districtId;
            
            // Pastikan nilai input hidden sudah terisi dengan nilai yang ada di database
            $('#province_name').val(userProvince);
            $('#city_name').val(userCity);
            $('#district_name').val(userDistrict);
            $('#village_name').val(userVillage);
            
            // Fetch provinces
            $.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', function(provinces) {
                $('#province').empty().append(new Option('Pilih Provinsi', ''));
                
                provinces.forEach(function(province) {
                    var selected = (province.name === userProvince) ? 'selected' : '';
                    $('#province').append(new Option(province.name, province.id, false, selected));
                    
                    if (province.name === userProvince) {
                        provinceId = province.id;
                        
                        // Fetch cities if province is found
                        fetchCities(provinceId);
                    }
                });
                $('#province').select2('refresh');
            });
            
            // Function to fetch cities
            function fetchCities(provinceId) {
                $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`, function(cities) {
                    $('#city').empty().append(new Option('Pilih Kota/Kabupaten', ''));
                    
                    cities.forEach(function(city) {
                        var selected = (city.name === userCity) ? 'selected' : '';
                        $('#city').append(new Option(city.name, city.id, false, selected));
                        
                        if (city.name === userCity) {
                            cityId = city.id;
                            
                            // Fetch districts if city is found
                            fetchDistricts(cityId);
                        }
                    });
                    $('#city').select2('refresh');
                });
            }
            
            // Function to fetch districts
            function fetchDistricts(cityId) {
                $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`, function(districts) {
                    $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                    
                    districts.forEach(function(district) {
                        var selected = (district.name === userDistrict) ? 'selected' : '';
                        $('#district').append(new Option(district.name, district.id, false, selected));
                        
                        if (district.name === userDistrict) {
                            districtId = district.id;
                            
                            // Fetch villages if district is found
                            fetchVillages(districtId);
                        }
                    });
                    $('#district').select2('refresh');
                });
            }
            
            // Function to fetch villages
            function fetchVillages(districtId) {
                $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`, function(villages) {
                    $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                    
                    villages.forEach(function(village) {
                        var selected = (village.name === userVillage) ? 'selected' : '';
                        $('#village').append(new Option(village.name, village.id, false, selected));
                    });
                    $('#village').select2('refresh');
                });
            }
            
            // Handler for province change
            $('#province').change(function() {
                var provinceId = $(this).val();
                var provinceName = $('#province option:selected').text();
                $('#province_name').val(provinceName);
                
                $('#city').empty().append(new Option('Pilih Kota/Kabupaten', ''));
                $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                
                // Reset hidden inputs
                $('#city_name').val('');
                $('#district_name').val('');
                $('#village_name').val('');
                
                if (provinceId) {
                    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`, function(cities) {
                        cities.forEach(function(city) {
                            $('#city').append(new Option(city.name, city.id));
                        });
                        $('#city').select2('refresh');
                    });
                }
            });

            // Handler for city change
            $('#city').change(function() {
                var cityId = $(this).val();
                var cityName = $('#city option:selected').text();
                $('#city_name').val(cityName);
                
                $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                
                // Reset hidden inputs
                $('#district_name').val('');
                $('#village_name').val('');
                
                if (cityId) {
                    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`, function(districts) {
                        districts.forEach(function(district) {
                            $('#district').append(new Option(district.name, district.id));
                        });
                        $('#district').select2('refresh');
                    });
                }
            });

            // Handler for district change
            $('#district').change(function() {
                var districtId = $(this).val();
                var districtName = $('#district option:selected').text();
                $('#district_name').val(districtName);
                
                $('#village').empty().append(new Option('Pilih Desa/Kelurahan', ''));
                
                // Reset hidden input
                $('#village_name').val('');
                
                if (districtId) {
                    $.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`, function(villages) {
                        villages.forEach(function(village) {
                            $('#village').append(new Option(village.name, village.id));
                        });
                        $('#village').select2('refresh');
                    });
                }
            });
            
            // Handler for village change
            $('#village').change(function() {
                var villageName = $('#village option:selected').text();
                $('#village_name').val(villageName);
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