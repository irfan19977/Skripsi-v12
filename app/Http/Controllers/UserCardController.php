<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserCardController extends Controller
{
    public function student()
{
    $students = User::when(request()->q, function($students) {
        $students->where('users.name', 'like', '%'. request()->q . '%')
                ->orWhere('users.nisn', 'like', '%' . request()->q . '%');
    })->join('student_class', 'users.id', '=', 'student_class.student_id')
        ->join('class', 'student_class.class_id', '=', 'class.id')
        ->with('classRoom')
        ->role('student')
        ->select('users.*')
        ->orderBy('class.grade', 'asc')
        ->orderByRaw("CASE 
            WHEN class.prodi = 'Akuntansi' THEN 1 
            WHEN class.prodi = 'Teknik Komputer dan Jaringan' THEN 2 
            WHEN class.prodi = 'Design Komunikasi Visual' THEN 3 
            WHEN class.prodi = 'Asisten Keperawatan' THEN 4 
            ELSE 5 END")
        ->orderBy('users.name', 'asc')
        ->distinct()
        ->paginate(10);
        
    return view('user.card.student', compact('students'));
}

    // Add this method to your StudentController
    public function stprint(Request $request)
    {
        // Get student IDs from request
        $studentIds = $request->input('students');
        
        if (empty($studentIds)) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak ada siswa yang dipilih untuk dicetak kartu.');
        }
        
        // Convert comma-separated string to array
        $studentIdsArray = explode(',', $studentIds);
        
        // Get the selected students
        $students = User::whereIn('id', $studentIdsArray)->get();
        
        if ($students->isEmpty()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menemukan siswa yang dipilih.');
        }
        
        // Return the view with the selected students
        return view('user.card.studentpr', compact('students'));
    }

    // UNTUK TEACHER

    public function teacher()
    {
        $teachers = User::latest()->when(request()->q, function($teachers) {
            $teachers->where('users.name', 'like', '%'. request()->q . '%')
                    ->orWhere('users.nip', 'like', '%' . request()->q . '%');
        })
            ->role('teacher')
            ->select('users.*')
            ->orderBy('users.name', 'asc')
            ->distinct()
            ->paginate(10);
        
        return view('user.card.teacher', compact('teachers'));
    }

    public function tcprint(Request $request)
    {
        // Get student IDs from request
        $teacherIds = $request->input('students');
        
        if (empty($teacherIds)) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak ada siswa yang dipilih untuk dicetak kartu.');
        }
        
        // Convert comma-separated string to array
        $teacherIdsArray = explode(',', $teacherIds);
        
        // Get the selected students
        $teachers = User::whereIn('id', $teacherIdsArray)->get();
        
        if ($teachers->isEmpty()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menemukan siswa yang dipilih.');
        }
        
        // Return the view with the selected students
        return view('user.card.teacherpr', compact('teachers'));
    }
}
