<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    // Filter properties
    public $class_id = '';
    public $subject_id = '';
    public $start_date = '';
    public $end_date = '';
    public $search = '';
    public $filteredSubjects = [];
    public $selectedSubject = '';
    public $isTeacher = false;
    public $teacherSubjects = [];
    public $teacherStudents = [];
    public $attendanceDates = [];
    public $teacherAttendances = [];
    public $currentDayOfWeek = '';
    
    // Listener untuk event
    protected $listeners = ['refresh' => '$refresh'];
    
    protected $queryString = ['class_id', 'subject_id', 'start_date', 'end_date', 'search', 'selectedSubject'];
    
    public function mount()
    {
        // Check if the current user is a teacher
        $this->isTeacher = Auth::user()->hasRole('Teacher');
        
        // Get current day of week
        $this->currentDayOfWeek = Carbon::now()->locale('id')->dayName;
        
        // Set default values from request if available
        $this->class_id = request('class_id', '');
        $this->subject_id = request('subject_id', '');
        $this->start_date = request('start_date', '');
        $this->end_date = request('end_date', '');
        $this->search = request('q', '');
        $this->selectedSubject = request('selectedSubject', '');
        
        // Initialize filtered subjects
        $this->updateFilteredSubjects();
        
        // If user is a teacher, load their subjects and set default subject
        if ($this->isTeacher) {
            $this->loadTeacherSubjects();
            $this->setDefaultTeacherSubject();
        }
    }
    
    public function setDefaultTeacherSubject()
    {
        if (!empty($this->selectedSubject)) {
            // If subject is already selected (from query string), use that
            return;
        }
        
        $teacherId = Auth::id();
        $currentTime = Carbon::now()->format('H:i:s');
        
        // Try to find current ongoing class based on day and time
        $currentSchedule = Schedule::where('teacher_id', $teacherId)
            ->where('day', $this->currentDayOfWeek)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->with('subject')
            ->first();
        
        if ($currentSchedule) {
            // If there's a current class, use that subject
            $this->selectedSubject = $currentSchedule->subject_id;
        } else {
            // If no current class, try to find the latest class for today
            $latestSchedule = Schedule::where('teacher_id', $teacherId)
                ->where('day', $this->currentDayOfWeek)
                ->where(function($query) use ($currentTime) {
                    $query->where('start_time', '<=', $currentTime)
                        ->orWhere('start_time', '>', $currentTime);
                })
                ->orderBy('start_time', 'desc')
                ->with('subject')
                ->first();
            
            if ($latestSchedule) {
                $this->selectedSubject = $latestSchedule->subject_id;
            } else if (count($this->teacherSubjects) > 0) {
                // If no schedule for today, use the first subject in the list
                $this->selectedSubject = $this->teacherSubjects[0]['id'];
            }
        }
        
        // Load attendance data for the selected subject
        if (!empty($this->selectedSubject)) {
            $this->loadTeacherAttendanceData();
        }
    }
    
    public function resetFilters()
    {
        $this->reset(['class_id', 'subject_id', 'start_date', 'end_date', 'search']);
        $this->resetPage();
        $this->updateFilteredSubjects();
    }
    
    public function updatedClassId()
    {
        $this->resetPage();
        $this->subject_id = ''; // Reset selected subject when class changes
        $this->updateFilteredSubjects();
    }
    
    public function updatedSubjectId()
    {
        $this->resetPage();
    }
    
    public function updatedStartDate()
    {
        $this->resetPage();
        if ($this->isTeacher && !empty($this->selectedSubject)) {
            $this->loadTeacherAttendanceData();
        }
    }
    
    public function updatedEndDate()
    {
        $this->resetPage();
        if ($this->isTeacher && !empty($this->selectedSubject)) {
            $this->loadTeacherAttendanceData();
        }
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedSubject()
    {
        if ($this->isTeacher && !empty($this->selectedSubject)) {
            $this->loadTeacherAttendanceData();
        }
    }
    
    public function updateFilteredSubjects()
    {
        if (!empty($this->class_id)) {
            // Get subjects taught in the selected class using the Schedule model
            $this->filteredSubjects = Schedule::where('class_id', $this->class_id)
                ->with('subject')
                ->get()
                ->pluck('subject')
                ->unique('id')
                ->toArray();
        } else {
            // If no class selected, get all subjects
            $this->filteredSubjects = Subject::all()->toArray();
        }
    }
    
    public function loadTeacherSubjects()
    {
        // Get all subjects taught by the current teacher
        $teacherId = Auth::id();
        $this->teacherSubjects = Schedule::where('teacher_id', $teacherId)
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->unique('id')
            ->toArray();
    }
    
    public function loadTeacherAttendanceData()
    {
        if (empty($this->selectedSubject)) {
            $this->teacherStudents = [];
            $this->attendanceDates = [];
            $this->teacherAttendances = [];
            return;
        }
        
        $teacherId = Auth::id();
        
        // Get all classes where this teacher teaches this subject
        $classIds = Schedule::where('teacher_id', $teacherId)
            ->where('subject_id', $this->selectedSubject)
            ->pluck('class_id')
            ->toArray();
        
        // Get all students in these classes
        $this->teacherStudents = User::whereHas('studentClasses', function($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            })
            ->whereHas('roles', function($query) {
                $query->where('name', 'Student');
            })
            ->select('id', 'name', 'nisn')
            ->orderBy('name')
            ->get()
            ->toArray();
        
        // Get date range for attendance
        $today = Carbon::today()->format('Y-m-d');
        $startDate = $this->start_date ?: Attendance::min('date'); // Use the earliest attendance date
        $endDate = $this->end_date ?: Attendance::max('date'); // Use the latest attendance date
        
        // Get all attendance dates for this subject by this teacher
        $this->attendanceDates = Attendance::where('teacher_id', $teacherId)
            ->where('subject_id', $this->selectedSubject)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->pluck('date')
            ->unique()
            ->toArray();
        
        // If no attendance dates are found, default to today
        if (empty($this->attendanceDates)) {
            $this->attendanceDates = [$today];
        }
        
        // Get all attendances for these students in this subject by this teacher
        $studentIds = collect($this->teacherStudents)->pluck('id')->toArray();
        
        $attendances = Attendance::where('teacher_id', $teacherId)
            ->where('subject_id', $this->selectedSubject)
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('id', 'student_id', 'date', 'status')
            ->get();
        
        // Group attendances by student_id
        $this->teacherAttendances = [];
        foreach ($attendances as $attendance) {
            if (!isset($this->teacherAttendances[$attendance->student_id])) {
                $this->teacherAttendances[$attendance->student_id] = [];
            }
            $this->teacherAttendances[$attendance->student_id][] = [
                'id' => $attendance->id,
                'date' => $attendance->date,
                'status' => $attendance->status
            ];
        }
    }
    
    public function render()
    {
        // Ambil data kelas dan mata pelajaran
        $classes = Classes::all();
        
        // For teacher view, load the attendance data
        if ($this->isTeacher && !empty($this->selectedSubject)) {
            $this->loadTeacherAttendanceData();
        }
        
        // For admin view, get attendance data with filtering
        $attendances = Attendance::query()
            ->with(['student', 'subject', 'teacher'])
            ->when($this->class_id, function ($query) {
                return $query->whereHas('student.studentClasses', function ($q) {
                    $q->where('class_id', $this->class_id);
                });
            })
            ->when($this->subject_id, function ($query) {
                return $query->where('subject_id', $this->subject_id);
            })
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->search && $this->search !== '', function ($query) {
                return $query->where(function($q) {
                    $q->whereHas('student', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('nisn', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->isTeacher, function ($query) {
                // If user is a teacher, only show their attendances
                return $query->where('teacher_id', Auth::id());
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('livewire.attendance-table', [
            'attendances' => $attendances,
            'classes' => $classes,
            'filteredSubjects' => collect($this->filteredSubjects),
            'isTeacher' => $this->isTeacher,
            'teacherSubjects' => collect($this->teacherSubjects),
            'teacherStudents' => $this->teacherStudents,
            'attendanceDates' => $this->attendanceDates,
            'teacherAttendances' => $this->teacherAttendances
        ]);
    }
}
