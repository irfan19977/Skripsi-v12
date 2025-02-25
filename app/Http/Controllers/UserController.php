<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->when(request()->q, function($subjects) {
            $subjects = $subjects->where('name', 'like', '%'. request()->q . '%')
            ->orWhere('code', 'like', '%' . request()->q . '%');
        })->paginate(10);

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nisn' => 'nullable|string|max:20|unique:users,nisn',
            'nip' => 'nullable|string|max:20|unique:users,nip',
            'no_kartu' => 'nullable|string|max:50|unique:users,no_kartu',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|string|exists:roles,name',
            'province' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'village' => 'nullable|string',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Create new user
        $users = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'nisn' => $request->input('nisn'),
            'nip' => $request->input('nip'),
            'no_kartu' => $request->input('no_kartu'),
            'phone' => $request->input('phone'),
            'province' => $request->input('province'),
            'city' => $request->input('city_name') ?: $request->input('city'),
            'district' => $request->input('district_name') ?: $request->input('district'),
            'village' => $request->input('village_name') ?: $request->input('village'),
            'address' => $request->input('address'),
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        
        // Upload Foto
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('photos', $fileName, 'public');
            $users->photo = $filePath;
            $users->save();
        }
        
        // Assign role to user
        $users->assignRole($request->input('role'));
        
        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
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
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRole = $user->roles->pluck('name')->first();
        
        
        return view('user.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate form inputs
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'nisn' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:20',
            'no_kartu' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|string|exists:roles,name',
            'province_name' => 'nullable|string|max:100',
            'city_name' => 'nullable|string|max:100',
            'district_name' => 'nullable|string|max:100',
            'village_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validatedData = $request->validate($rules);

        // Find the user
        $user = User::findOrFail($id);

        // Update user data
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        
        // Only update password if provided
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->nisn = $validatedData['nisn'] ?? null;
        $user->nip = $validatedData['nip'] ?? null;
        $user->no_kartu = $validatedData['no_kartu'] ?? null;
        $user->phone = $validatedData['phone'] ?? null;
        $user->province = $validatedData['province_name'] ?? null;
        $user->city = $validatedData['city_name'] ?? null;
        $user->district = $validatedData['district_name'] ?? null;
        $user->village = $validatedData['village_name'] ?? null;
        $user->address = $validatedData['address'] ?? null;
        $user->is_active = $request->has('is_active') ? 1 : 0;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Store new photo
            $photoPath = $request->file('photo')->store('users', 'public');
            $user->photo = $photoPath;
        }

        // Save user data
        $user->save();

        // Update user role
        $user->syncRoles([$validatedData['role']]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Delete photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Delete user
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }


    public function toggleActive(Request $request, User $user)
    {
            // Toggle the status
            $user->is_active = !$user->is_active;
            $saved = $user->save();
            
            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan perubahan status'
                ], 500);
            }
            
            // Return with current status after save
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'is_active' => $user->is_active,
                'status_text' => $user->is_active ? 'Active' : 'Diblokir'
            ]);
    }
}
