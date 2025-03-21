<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classes::latest()->when(request()->q, function($classes) {
            $classes = $classes->where('name', 'like', '%'. request()->q . '%');
        })->paginate(10);

        // Count the number of students in each class
        foreach ($classes as $class) {
            $class->student_count = StudentClass::where('class_id', $class->id)->count();
        }
        
        return view('class.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('class.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prodi' => 'required',
            'name' => 'required',
            'grade' => 'required'
        ]);

        // Check if the name already exists
        if (Classes::where('name', $request->input('name'))->exists()) {
            return redirect()->route('class.create')->withInput()->withErrors(['name' => 'Nama sudah tersedia, tidak bisa membuat nama yang sama']);
        }

        $classes = Classes::create([
            'prodi' => $request->input('prodi'),
            'name' => $request->input('name'),
            'grade' => $request->input('grade')
        ]);

        if ($classes) {
            return redirect()->route('class.index')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect()->route('class.index')->with(['error' => 'Data Gagal Disimpan']);
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
        $classes = Classes::findOrFail($id);

        return view('class.edit', compact('classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'prodi' => 'required',
            'name' => 'required',
            'grade' => 'required'
        ]);

        // Ambil data kelas yang akan diupdate
        $class = Classes::findOrFail($id);

        // Check if the name already exists (excluding the current class)
        if (Classes::where('name', $request->input('name'))
            ->where('id', '!=', $id)
            ->exists()) {
            return redirect()
                ->route('class.edit', $id)
                ->withInput()
                ->withErrors(['name' => 'Nama sudah tersedia, tidak bisa menggunakan nama yang sama']);
        }

        // Update data
        $updated = $class->update([
            'prodi' => $request->input('prodi'),
            'name' => $request->input('name'),
            'grade' => $request->input('grade')
        ]);

        if ($updated) {
            return redirect()->route('class.index')->with(['success' => 'Data Berhasil Diperbarui']);
        } else {
            return redirect()->route('class.index')->with(['error' => 'Data Gagal Diperbarui']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classes = Classes::findOrFail($id);

        // Check if there are students in the class
        if (StudentClass::where('class_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak bisa dihapus karena masih ada siswa di dalamnya']);
        }

        $classes->delete();

        if ($classes) {
            return response()->json(['success' => true, 'message' => 'Data Berhasil Dihapus']);
        } else {
            return response()->json(['success' => false, 'message' => 'Data Gagal Dihapus']);
        }
    }

    public function editAssign($id)
    {
        // Find the class
        $class = Classes::findOrFail($id);
    
        // Get students in this class
        $assignedStudents = StudentClass::where('class_id', $class->id)
            ->with('student')
            ->get();
    
        // Get students not assigned to any class
        $availableStudents = User::role('student')
            ->whereDoesntHave('studentClasses')  // Cek siswa belum masuk kelas manapun
            ->get();
    
        return view('class.edit-assign', compact('class', 'assignedStudents', 'availableStudents'));
    }
 

    public function updateAssign(Request $request, $id)
    {
        $class = Classes::findOrFail($id);

        // Remove existing students if needed
        if ($request->has('remove_student_ids')) {
            StudentClass::where('class_id', $class->id)
                ->whereIn('student_id', $request->input('remove_student_ids'))
                ->delete();
        }

        // Add new students
        if ($request->has('student_ids')) {
            foreach ($request->input('student_ids') as $studentId) {
                StudentClass::create([
                    'class_id' => $class->id,
                    'student_id' => $studentId,
                ]);
            }
        }

        return redirect()->route('class.edit-assign', $class->id)
            ->with('success', 'Daftar siswa kelas berhasil diperbarui');
    }
}
