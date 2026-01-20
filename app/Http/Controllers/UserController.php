<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    public function index()
    {
        // $users = User::with(['roles'])->paginate(10);
        return view('pages.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view("pages.users.create", compact(['roles']));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $data = [
                "username" => $request->username,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "is_active" => true,
            ];

            $user = User::create($data);
            $user->syncRoles($request->role_id);

            DB::commit();
            return redirect()->route('pengguna.index')->with('message', "saved");
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal simpan pengguna', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view("pages.users.edit", compact(['user', 'roles']));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            $user = User::findOrFail($id);

            // Update data utama
            $user->username = $request->username;
            $user->email = $request->email;
            $user->is_active = $request->has('activation') ? true : false;

            // Jika password diisi, update
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Update roles
            $user->syncRoles($request->role_id ?? []);

            DB::commit();
            return redirect()->route('pengguna.index')->with('message', 'Data pengguna berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update pengguna', 'error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            // Hapus semua role yang terkait (opsional, karena cascade dari spatie bisa juga)
            $user->syncRoles([]);

            $user->delete();

            DB::commit();
            return redirect()->route('pengguna.index')->with('message', 'Data pengguna berhasil dihapus!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus pengguna: ' . $e->getMessage()]);
        }
    }

    public function profile()
    {
        $user = Auth::user();
        $roles = Role::all();
        return view("pages.users.edit", compact(['user', 'roles']));
    }

    public function updaterofile(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            $user = Auth::user();

            // Update data utama
            $user->username = $request->username;
            $user->email = $request->email;
            $user->is_active = $request->has('activation') ? true : false;

            // Jika password diisi, update
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Update roles
            $user->syncRoles($request->role_id ?? []);

            DB::commit();
            return redirect()->route('pengguna.index')->with('message', 'Data pengguna berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update pengguna', 'error' => $e->getMessage()], 500);
        }
    }
}
