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
                                        placeholder="Masukkan No Kartu" value="{{ old('no_kartu') }}">
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
                                    <select id="province" class="form-control select2 @error('province') is-invalid @enderror" name="province">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <input type="hidden" name="province" id="province_name">
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
                                    <input type="hidden" name="city" id="city_name">
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
                                    <input type="hidden" name="district" id="district_name">
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
                                    <input type="hidden" name="village" id="village_name">
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
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo">
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
    <script>
        $(document).ready(function() {
            // Fetch provinces
            $.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', function(provinces) {
                provinces.forEach(function(province) {
                    $('#province').append(new Option(province.name, province.id));
                });
                $('#province').select2('refresh');
            });

            // Fetch cities when province is selected
            $('#province').change(function() {
                var provinceId = $(this).val();
                var provinceName = $('#province option:selected').text();
                $('#province_name').val(provinceName);
                
                $('#city').empty().append(new Option('Pilih Kota/Kabupaten', ''));
                $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                $('#village').empty().append(new Option('Pilih Desa', ''));
                
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

            // Fetch districts when city is selected
            $('#city').change(function() {
                var cityId = $(this).val();
                var cityName = $('#city option:selected').text();
                $('#city_name').val(cityName);
                
                $('#district').empty().append(new Option('Pilih Kecamatan', ''));
                $('#village').empty().append(new Option('Pilih Desa', ''));
                
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

            // Fetch villages when district is selected
            $('#district').change(function() {
                var districtId = $(this).val();
                var districtName = $('#district option:selected').text();
                $('#district_name').val(districtName);
                
                $('#village').empty().append(new Option('Pilih Desa', ''));
                
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
            
            // Save village name when selected
            $('#village').change(function() {
                var villageName = $('#village option:selected').text();
                $('#village_name').val(villageName);
            });
        });
    </script>
@endpush