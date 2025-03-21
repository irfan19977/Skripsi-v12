<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hanya mempersiapkan data untuk digunakan oleh komponen Livewire
        $classes = Classes::all();
        $subjects = Subject::all();
        
        return view('attendance.index', compact('classes', 'subjects'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = User::role('student')->get();
        $teachers = User::role('teacher')->get();
        $subjects = Subject::all();
        $currentDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');
        
        return view('attendance.create', compact('students', 'teachers', 'subjects', 'currentDate', 'currentTime'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id', 
            'teacher_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check for existing attendance
        $attendanceExists = Attendance::where('student_id', $validatedData['student_id'])
            ->where('subject_id', $validatedData['subject_id'])
            ->where('date', $validatedData['date'])
            ->exists();

        if ($attendanceExists) {
            return redirect()->back()
                ->with('error', 'Siswa sudah melakukan absensi untuk mata pelajaran ini pada tanggal tersebut.')
                ->withInput();
        }

        // Set teacher_id if not provided
        $validatedData['teacher_id'] = $validatedData['teacher_id'] ?? Auth::id();

        // Create attendance record
        $attendance = Attendance::create($validatedData);

        if($attendance) {
            return redirect()->route('attendances.index')
                ->with('success', 'Absensi berhasil dicatat.');
        }

        return redirect()->back()
            ->with('error', 'Gagal menyimpan data absensi.')
            ->withInput();
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $attendances = Attendance::findOrFail($id);
         // Get students, subjects, and statuses
         $students = User::role('student')->get();
         $teachers = User::role('teacher')->get();
         $subjects = Subject::all();
         // Tanggal dan waktu sekarang menggunakan Carbon
         $currentDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
         $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');
 
         return view('attendance.edit', compact('attendances', 'students', 'teachers', 'subjects', 'currentDate', 'currentTime'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attendances = Attendance::where('id', $id)->firstOrFail();
        $attendances->update([
            'status' => $request->input('status'),
            'notes' => $request->input('notes')
        ]);
       
        // Redirect with success message
        return redirect()->route('attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendances = Attendance::findOrFail($id);
        $attendances->delete();

        if ($attendances) {
            return response()->json(['success' => true, 'Message' => 'Data Berhasil Dihapus']);
        } else{
            return response()->json(['success' => false, 'Message' => 'Data Gagal Dihapus']);
        }
    }

    public function findByNisn($nisn) {
        // Get current time and day using Carbon
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i:s');
        $currentDay = $now->format('l');
    
        // Day mapping
        $dayMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        $indonesianDay = $dayMap[$currentDay];
    
        // Find student
        $student = User::with('classRoom')
            ->where('nisn', $nisn)
            ->first();
    
        if (!$student) {
            return response()->json([
                'message' => 'Siswa dengan NISN tersebut tidak ditemukan'
            ], 404);
        }
    
        // Get student's class ID
        $studentClassId = optional($student->classRoom->first())->id;
    
        if (!$studentClassId) {
            return response()->json([
                'message' => 'Siswa tidak memiliki kelas'
            ], 404);
        }
    
        // Find all schedules for the student's class on the current day
        $allSchedules = Schedule::where('class_id', $studentClassId)
            ->where('day', $indonesianDay)
            ->get();
    
        // Find the current schedule using direct time comparison
        $schedule = $allSchedules->first(function ($schedule) use ($currentTime) {
            return $currentTime >= $schedule->start_time && $currentTime <= $schedule->end_time;
        });
    
        if (!$schedule) {
            return response()->json([
                'message' => 'Tidak ada jadwal pelajaran saat ini',
            ], 404);
        }
    
        // Check if student has already attended this subject today
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $schedule->subject_id)
            ->whereDate('date', now())
            ->exists();
    
        if ($existingAttendance) {
            return response()->json([
                'message' => 'Siswa sudah melakukan absensi untuk mata pelajaran ini hari ini'
            ], 400);
        }
    
        // Return student and schedule data
        return response()->json([
            'id' => $student->id,
            'name' => $student->name,
            'nisn' => $student->nisn,
            'class_name' => $student->classRoom->first()->name ?? 'Tidak ada kelas',
            'subject_id' => $schedule->subject_id,
            'subject_name' => $schedule->subject->name,
            'teacher_id' => $schedule->teacher_id,
            'teacher_name' => $schedule->teacher->name,
            'current_schedule' => [
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time
            ]
        ]);
    }

    public function scanQRAttendance(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'nisn' => 'required|string'
            ]);

            // Use the existing findByNisn method to get student details and validate attendance
            $response = $this->findByNisn($validatedData['nisn']);

            // Check if the response is successful (200 status code)
            if ($response->getStatusCode() === 200) {
                $studentData = json_decode($response->getContent(), true);

                // Prepare attendance data
                $attendanceData = [
                    'student_id' => $studentData['id'],
                    'subject_id' => $studentData['subject_id'],
                    'teacher_id' => $studentData['teacher_id'],
                    'date' => now('Asia/Jakarta')->format('Y-m-d'),
                    'time' => now('Asia/Jakarta')->format('H:i'),
                    'status' => 'hadir', // Default status is 'hadir' (present)
                ];

                // Create attendance record
                $attendance = Attendance::create($attendanceData);

                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Absensi berhasil dicatat.',
                    'student' => [
                        'name' => $studentData['name'],
                        'class' => $studentData['class_name'],
                        'subject' => $studentData['subject_name'],
                        'teacher' => $studentData['teacher_name'],
                    ]
                ]);
            }

            // If findByNisn returns an error response, return that response
            return $response;

        } catch (\Exception $e) {
            // Log the error
            Log::error('QR Attendance Scan Error: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan absensi: ' . $e->getMessage()
            ], 500);
        }
    }
}
