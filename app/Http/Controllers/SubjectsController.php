<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use illuminate\Support\Str;

class SubjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::latest()->when(request()->q, function($subjects) {
            $subjects = $subjects->where('name', 'like', '%'. request()->q . '%')
            ->orWhere('code', 'like', '%' . request()->q . '%');
        })->paginate(10);

        return view('subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
        // Generate fully random product code
        $productCode = Str::upper(Str::random(3)) . rand(100, 999);

        $subjects = Subject::create([
            'name' => $request->input('name'),
            'code' => $productCode,
            'description' => $request->input('description'),
        ]);

        if ($subjects) {
            return redirect()->route('subjects.index')->with(['success' => 'Data Berhasil Ditambahkan!!']);
        } else {
            return redirect()->route('subjects.index')->with(['Error' => 'Data Gagal Ditambahkan']);
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
        $subjects = Subject::findOrFail($id);

        return view('subject.edit', compact('subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = request()->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $subjects = Subject::findOrFail($id);
        $subjects->update([
            'name' => $request->input('name'),
            'description' => $request->input('description')
        ]);

        if ($subjects) {
            return redirect()->route('subjects.index')->with(['success' => 'Data Berhasil Diperbarui!!']);
        } else {
            return redirect()->route('subjects.index')->with(['error' => 'Data Gagal Diperbarui']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subjects = Subject::findOrFail($id);
        $subjects->delete();

        if ($subjects) {
            return response()->json(['success' => true, 'message' => 'Data Berhasil Dihapus']);
        } else {
            return response()->json(['success' => false, 'massage' => 'Data Gagal Dihapus']);
        }
    }
}
