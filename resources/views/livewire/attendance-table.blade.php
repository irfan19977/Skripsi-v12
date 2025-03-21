<div wire:poll.1ms>
    <!-- Admin Attendance Records -->
    @if(!$isTeacher)
    <div class="card">
        <div class="card-header">
            <h4>Filter Data Absensi</h4>
            <div class="card-header-action">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#qrScanModal">
                    <i class="fas fa-qrcode"></i> Scan QR Code
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="class_id">Kelas</label>
                    <select wire:model="class_id" id="class_id" class="form-control select2">
                        <option value="">Pilih Kelas</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="form-group col-md-3">
                    <label for="subject_id">Mata Pelajaran</label>
                    <select wire:model="subject_id" id="subject_id" class="form-control">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($filteredSubjects as $subject)
                            <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="start_date">Tanggal awal</label>
                    <input type="date" wire:model="start_date" id="start_date" class="form-control" max="{{ $end_date }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="end_date">Tanggal akhir</label>
                    <input type="date" wire:model="end_date" id="end_date" class="form-control" min="{{ $start_date }}">
                </div>
            </div>
            <div class="form-row mt-2">
                <div class="col text-right">
                    <button wire:click="resetFilters" class="btn btn-secondary">Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <h4>Data Absensi Siswa</h4>
            <div class="card-header-action">
                <div class="input-group">
                    <a href="{{ route('attendances.create') }}" class="btn btn-primary" data-toggle="tooltip" style="margin-right: 10px;" title="Tambah Data"><i class="fas fa-plus"></i></a>
                    <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="Cari nama siswa">
                    <div class="input-group-btn">
                      <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                  </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped" id="attendance-table">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>NAMA SISWA</th>
                            <th>PELAJARAN</th>
                            <th>PENGAJAR</th>
                            <th>TANGGAL</th>
                            <th>WAKTU</th>
                            <th>STATUS</th>
                            <th>CATATAN</th>
                            @can('attendances.edit')
                                <th>AKSI</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr wire:key="attendance-{{ $attendance->id }}">
                                <td class="text-center">
                                    {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                                </td>
                                <td>{{ ucwords(strtolower($attendance->student->name)) }}</td>
                                <td>{{ $attendance->subject->name }}</td>
                                <td>{{ $attendance->teacher->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                                <td>{{ $attendance->time }}</td>
                                <td>
                                    @if ($attendance->status == 'Hadir')
                                        <span class="badge badge-success">{{ $attendance->status }}</span>
                                    @elseif ($attendance->status == 'Izin')
                                        <span class="badge badge-info">{{ $attendance->status }}</span>
                                    @elseif ($attendance->status == 'Sakit')
                                        <span class="badge badge-warning">{{ $attendance->status }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $attendance->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $attendance->notes ?? '-' }}</td>
                                @can('attendances.edit')
                                    <td>
                                        <a href="{{ route('attendances.edit', $attendance) }}" 
                                            class="btn btn-primary btn-action mr-1" 
                                            data-toggle="tooltip" 
                                            title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
            
                                        <form id="delete-form-{{ $attendance->id }}" action="{{ route('attendances.destroy', $attendance->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" onclick="confirmDelete('{{ $attendance->id }}')">
                                              <i class="fas fa-trash"></i>
                                            </button>
                                          </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data absensi yang ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Teacher Specific Attendance View -->
    @if($isTeacher)
    <div class="card">
        <div class="card-header">
            <h4>Filter Data Absensi</h4>
            <div class="card-header-action">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#qrScanModal">
                    <i class="fas fa-qrcode"></i> Scan QR Code
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label id="selectedSubject">Mata Pelajaran</label>
                    <select id="selectedSubject" wire:model="selectedSubject" class="form-control">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($teacherSubjects as $subject)
                            <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="start_date">Tanggal awal</label>
                    <input type="date" wire:model="start_date" id="start_date" class="form-control" max="{{ $end_date }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">Tanggal akhir</label>
                    <input type="date" wire:model="end_date" id="end_date" class="form-control" min="{{ $start_date }}">
                </div>
            </div>
            <div class="form-row mt-2">
                <div class="col text-right">
                    <button wire:click="resetFilters" class="btn btn-secondary">Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Data Absensi Siswa</h4>
            <div class="card-header-action">
                <div class="input-group">
                    <a href="{{ route('attendances.create') }}" class="btn btn-primary" data-toggle="tooltip" style="margin-right: 10px;" title="Tambah Data"><i class="fas fa-plus"></i></a>
                    <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Cari nama siswa">
                    <div class="input-group-btn">
                      <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                  </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped" id="attendance-table">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>NAMA SISWA</th>
                            @foreach($attendanceDates as $date)
                                <th class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</th>
                            @endforeach
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teacherStudents as $index => $student)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ ucwords(strtolower($student['name'])) }}</td>
                                @foreach($attendanceDates as $date)
                                    <td class="text-center">
                                        @php
                                            $status = '-';
                                            $statusClass = '';
                                            
                                            if (isset($teacherAttendances[$student['id']])) {
                                                foreach ($teacherAttendances[$student['id']] as $attendance) {
                                                    if ($attendance['date'] == $date) {
                                                        $status = $attendance['status'];
                                                        
                                                        if ($status == 'Hadir') {
                                                            $statusClass = 'badge-success';
                                                        } elseif ($status == 'Izin') {
                                                            $statusClass = 'badge-info';
                                                        } elseif ($status == 'Sakit') {
                                                            $statusClass = 'badge-warning';
                                                        } else {
                                                            $statusClass = 'badge-danger';
                                                        }
                                                        
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @if($status != '-')
                                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    @php
                                        $todayAttendanceId = null;
                                        $today = \Carbon\Carbon::today()->format('Y-m-d');
                                        
                                        if (isset($teacherAttendances[$student['id']])) {
                                            foreach ($teacherAttendances[$student['id']] as $attendance) {
                                                if ($attendance['date'] == $today) {
                                                    $todayAttendanceId = $attendance['id'];
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    @if($todayAttendanceId)
                                        <a href="{{ route('attendances.edit', $todayAttendanceId) }}" 
                                        class="btn btn-primary btn-action mr-1" 
                                        data-toggle="tooltip" 
                                        title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-primary btn-action mr-1" 
                                                disabled 
                                                data-toggle="tooltip" 
                                                title="Hanya bisa edit untuk hari ini">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($attendanceDates) + 3 }}" class="text-center">Tidak ada data siswa yang ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('showDeleteConfirmation', attendanceId => {
                $('#deleteConfirmationModal').modal('show');
                
                document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                    Livewire.emit('deleteAttendance', attendanceId);
                    $('#deleteConfirmationModal').modal('hide');
                });
            });
        });

        // Handle select2 initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined') {
                $('.select2').select2();
                $('.select2').on('change', function (e) {
                    @this.set('class_id', e.target.value);
                });
            } else {
                console.error('jQuery is not defined. Make sure jQuery is loaded before this script.');
            }
        });
        
        // Re-initialize select2 when Livewire updates
        document.addEventListener("livewire:update", function() {
            if (typeof $ !== 'undefined') {
                $('.select2').select2();
            }
        });
    </script>
</div>