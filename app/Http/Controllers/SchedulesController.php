<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::with(['subject', 'teacher', 'classRoom'])
                ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('start_time')
                ->paginate(10);

        return view('schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all(); // Mengambil semua mata pelajaran
        $teachers = User::role('teacher')->get(); // Mengambil semua guru
        $classRooms = Classes::all(); 
        
        return view('schedules.create', compact('subjects', 'teachers', 'classRooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required',
            'class_id' => 'required',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'academic_year' => 'required|string|max:10',
        ]);

        // Cek apakah jadwal bentrok dengan jadwal lain (guru mengajar di kelas lain pada waktu yang sama)
        $conflictingTeacherSchedule = Schedule::where('teacher_id', $request->teacher_id)
        ->where('day', $request->day)
        ->where(function ($query) use ($request) {
            $query->where(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
                });
        })->first();

        if ($conflictingTeacherSchedule) {
            return redirect()->back()
                ->with('error', 'Guru sudah memiliki jadwal mengajar pada waktu yang sama!')
                ->withInput();
        }

        // Cek apakah kelas sudah memiliki jadwal pada waktu yang sama
        $conflictingClassSchedule = Schedule::where('class_id', $request->class_id)
            ->where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->where(function($q) use ($request) {
                        $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                    });
            })->first();

        if ($conflictingClassSchedule) {
            return redirect()->back()
                ->with('error', 'Kelas sudah memiliki jadwal pelajaran pada waktu yang sama!')
                ->withInput();
        }

        $schedules = Schedule::create([
            'subject_id'     => $request->input('subject_id'),
            'teacher_id'     => $request->input('teacher_id'),
            'class_id'  => $request->input('class_id'),
            'day'            => $request->input('day'),
            'start_time'     => $request->input('start_time'),
            'end_time'     => $request->input('end_time'),
            'academic_year'     => $request->input('academic_year'),
        ]);

        if ($schedules) {
            return redirect()->route('schedules.index')->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->route('schedules.index')->with('error', 'Data Gagal Disimpan');
        }
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
    // Ambil data jadwal yang akan diedit
    $schedule = Schedule::findOrFail($id);
    
    // Ambil data untuk dropdown
    $subjects = Subject::all();
    $teachers = User::role('teacher')->get();
    $classRooms = Classes::all();
    
    return view('schedules.edit', compact('schedule', 'subjects', 'teachers', 'classRooms'));
}

/**
 * Update the specified resource in storage.
 */
public function update(Request $request, string $id)
{
    // Validasi input
    $validated = $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'teacher_id' => 'required',
        'class_id' => 'required',
        'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'academic_year' => 'required|string|max:10',
    ]);

    $schedule = Schedule::findOrFail($id);
    
    // Cek apakah jadwal bentrok dengan jadwal lain (guru mengajar di kelas lain pada waktu yang sama)
    // Kecualikan jadwal yang sedang diedit dari pengecekan
    $conflictingTeacherSchedule = Schedule::where('teacher_id', $request->teacher_id)
        ->where('day', $request->day)
        ->where('id', '!=', $id)
        ->where(function ($query) use ($request) {
            $query->where(function($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            });
        })->first();

    if ($conflictingTeacherSchedule) {
        return redirect()->back()
            ->with('error', 'Guru sudah memiliki jadwal mengajar pada waktu yang sama!')
            ->withInput();
    }

    // Cek apakah kelas sudah memiliki jadwal pada waktu yang sama
    // Kecualikan jadwal yang sedang diedit dari pengecekan
    $conflictingClassSchedule = Schedule::where('class_id', $request->class_id)
        ->where('day', $request->day)
        ->where('id', '!=', $id)
        ->where(function ($query) use ($request) {
            $query->where(function($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            });
        })->first();

    if ($conflictingClassSchedule) {
        return redirect()->back()
            ->with('error', 'Kelas sudah memiliki jadwal pelajaran pada waktu yang sama!')
            ->withInput();
    }

    // Update jadwal
    $updated = $schedule->update([
        'subject_id'    => $request->input('subject_id'),
        'teacher_id'    => $request->input('teacher_id'),
        'class_id'      => $request->input('class_id'),
        'day'           => $request->input('day'),
        'start_time'    => $request->input('start_time'),
        'end_time'      => $request->input('end_time'),
        'academic_year' => $request->input('academic_year'),
    ]);

    if ($updated) {
        return redirect()->route('schedules.index')->with('success', 'Data Berhasil Diperbarui');
    } else {
        return redirect()->route('schedules.index')->with('error', 'Data Gagal Diperbarui');
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedules = Schedule::findOrFail($id);
        $schedules->delete();
        
        if ($schedules) {
            return response()->json(['success' => true, 'message' => 'Data Berhasil Dihapus']);
        } else {
            return response()->json(['success' => false, 'message' => 'Data Gagal Dihapus']);
        }
    }
}
