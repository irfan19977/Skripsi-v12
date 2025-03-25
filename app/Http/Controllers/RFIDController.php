<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RFIDController extends Controller
{
    public function detectRFID(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'rfid_card' => 'required|string'
            ]);
            
            $rfidCard = $request->input('rfid_card');
            
            // Find student by RFID card number
            $student = User::with('classRoom')
                ->where('no_kartu', $rfidCard)
                ->first();
            
            if (!$student) {
                // Check if the card is already registered to someone else
                $existingUser = User::where('no_kartu', $rfidCard)->first();
                $isUsed = $existingUser ? true : false;
                
                // Store the RFID value in cache with timestamp
                Cache::put('latest_rfid', [
                    'value' => $rfidCard,
                    'is_used' => $isUsed,
                    'user_name' => $isUsed ? $existingUser->name : null,
                    'timestamp' => Carbon::now()->timestamp
                ], now()->addSeconds(5)); // Keep in cache for 5 minutes
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kartu RFID tidak terdaftar',
                    'rfid_value' => $rfidCard
                ]);
            }
            
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
            
            // Get student's class ID
            $studentClassId = optional($student->classRoom->first())->id;
            
            if (!$studentClassId) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Siswa tidak memiliki kelas'
                ]);
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
                    'status' => 'error',
                    'message' => 'Tidak ada jadwal pelajaran saat ini',
                    'student' => [
                        'name' => $student->name,
                        'nisn' => $student->nisn,
                        'class' => $student->classRoom->first()->name ?? 'Tidak ada kelas',
                    ]
                ]);
            }
            
            // Check if student has already attended this subject today
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->where('subject_id', $schedule->subject_id)
                ->whereDate('date', now())
                ->exists();
            
            if ($existingAttendance) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Siswa sudah melakukan absensi untuk mata pelajaran ini hari ini'
                ]);
            }
            
            // Prepare attendance data
            $attendanceData = [
                'student_id' => $student->id,
                'subject_id' => $schedule->subject_id,
                'teacher_id' => $schedule->teacher_id,
                'date' => now('Asia/Jakarta')->format('Y-m-d'),
                'time' => now('Asia/Jakarta')->format('H:i'),
                'status' => 'hadir', // Default status is 'hadir' (present)
            ];
            
            // Create attendance record
            $attendance = Attendance::create($attendanceData);
            
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Absensi berhasil dicatat.',
                'student' => [
                    'name' => $student->name,
                    'nisn' => $student->nisn,
                    'class' => $student->classRoom->first()->name ?? 'Tidak ada kelas',
                    'subject' => $schedule->subject->name,
                    'teacher' => $schedule->teacher->name,
                ]
            ]);
            
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('RFID Attendance Error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan absensi: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get the latest RFID card detected
     * 
     * @return \Illuminate\Http\Response
     */
    public function getLatestRFID()
    {
        $latestRFID = Cache::get('latest_rfid');
        
        if (!$latestRFID) {
            return response()->json([
                'status' => 'error',
                'message' => 'No RFID card detected recently',
                'rfid' => null,
                'is_used' => false,
            ]);
        }
        
        // Only return RFID values that are relatively new (within the last minute)
        $now = Carbon::now()->timestamp;
        if ($now - $latestRFID['timestamp'] > 60) {
            return response()->json([
                'status' => 'error',
                'message' => 'RFID detection has expired',
                'rfid' => null,
                'is_used' => false,
            ]);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => $latestRFID['is_used'] ? 'RFID card is already in use' : 'RFID card detected',
            'rfid' => $latestRFID['value'],
            'is_used' => $latestRFID['is_used'],
            'user_name' => $latestRFID['user_name'] ?? null,
        ]);
    }
    
    /**
     * Clear the RFID cache
     * 
     * @return \Illuminate\Http\Response
     */
    public function clearRFIDCache()
    {
        Cache::forget('latest_rfid');
        
        return response()->json([
            'status' => 'success',
            'message' => 'RFID cache cleared successfully'
        ]);
    }
}
