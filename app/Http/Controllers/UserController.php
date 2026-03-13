<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Profile;
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
        $branches = Branch::all();

        return view("pages.users.create", compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                // USER
                'username' => ['required', 'string', 'max:255', 'unique:users,username'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],

                // PROFILE
                'nip' => ['nullable', 'string', 'max:50'],
                'nik' => ['nullable', 'string', 'max:50'],
                'nama' => ['required', 'string', 'max:255'],
                'alamat' => ['nullable', 'string'],
                'status' => ['nullable', 'string'],
                'no_hp' => ['nullable', 'string', 'max:20'],
                'no_rek' => ['nullable', 'string', 'max:50'],
                'nama_bank' => ['nullable', 'string', 'max:100'],
                'gaji' => ['nullable', 'numeric'],
                'branch_id' => ['nullable', 'exists:branches,id'],

                'role_id' => ['required'],
            ]);

            // ================= USER =================

            $user = User::create([
                "username" => $request->username,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "is_active" => $request->activation ? true : false,
            ]);

            $user->syncRoles($request->role_id);

            // ================= PROFILE =================

            Profile::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'status' => $request->status,
                'no_hp' => $request->no_hp,
                'no_rek' => $request->no_rek,
                'nama_bank' => $request->nama_bank,
                'gaji' => $request->gaji,
                'branch_id' => $request->branch_id,
            ]);

            DB::commit();

            return redirect()->route('pengguna.index')
                ->with('message', "Pengguna berhasil dibuat");
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        $roles = Role::all();
        $branches = Branch::all();

        return view("pages.users.edit", compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $request->validate([

                // USER
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],

                // PROFILE
                'nip' => ['nullable', 'string', 'max:50'],
                'nik' => ['nullable', 'string', 'max:50'],
                'nama' => ['required', 'string', 'max:255'],
                'alamat' => ['nullable', 'string'],
                'status' => ['nullable', 'string'],
                'no_hp' => ['nullable', 'string', 'max:20'],
                'no_rek' => ['nullable', 'string', 'max:50'],
                'nama_bank' => ['nullable', 'string', 'max:100'],
                'gaji' => ['nullable', 'numeric'],
                'branch_id' => ['nullable', 'exists:branches,id'],
            ]);

            $user = User::with('profile')->findOrFail($id);

            // ================= USER =================

            $user->username = $request->username;
            $user->email = $request->email;
            $user->is_active = $request->has('activation');

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Update role
            if ($request->has('role_id')) {
                $user->syncRoles($request->role_id);
            }

            // ================= PROFILE =================

            // Jika profile belum ada → buat
            $profile = $user->profile ?? new Profile();
            $profile->user_id = $user->id;

            $profile->nip = $request->nip;
            $profile->nik = $request->nik;
            $profile->nama = $request->nama;
            $profile->alamat = $request->alamat;
            $profile->status = $request->status;
            $profile->no_hp = $request->no_hp;
            $profile->no_rek = $request->no_rek;
            $profile->nama_bank = $request->nama_bank;
            $profile->gaji = $request->gaji;
            $profile->branch_id = $request->branch_id;

            $profile->save();

            DB::commit();

            return redirect()
                ->route('pengguna.index')
                ->with('status', 'edited');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $user = User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return back()->withErrors('Tidak bisa menghapus akun sendiri');
            }

            // Bersihkan role
            $user->syncRoles([]);

            // Hapus user (profile ikut terhapus via cascade)
            $user->delete();

            DB::commit();

            return redirect()
                ->route('pengguna.index')
                ->with('message', 'Data pengguna berhasil dihapus!');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => 'Gagal menghapus pengguna: ' . $e->getMessage()
            ]);
        }
    }

    public function profile()
    {
        $user = Auth::user()->load('profile');
        $branches = Branch::all();
        $roles = Role::all();

        return view("pages.users.profile", compact('user', 'branches', 'roles'));
    }

    public function updateProfile(Request $request)
    {
        DB::beginTransaction();

        try {

            $user = Auth::user();

            $request->validate([

                // USER
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],

                'password' => ['nullable', 'string', 'min:8', 'confirmed'],

                // PROFILE
                'nip' => ['nullable', 'string', 'max:50'],
                'nik' => ['nullable', 'string', 'max:50'],
                'nama' => ['required', 'string', 'max:255'],
                'alamat' => ['nullable', 'string'],
                'status' => ['nullable', 'string'],
                'no_hp' => ['nullable', 'string', 'max:20'],
                'no_rek' => ['nullable', 'string', 'max:50'],
                'nama_bank' => ['nullable', 'string', 'max:100'],
                'gaji' => ['nullable', 'numeric'],
                'branch_id' => ['nullable', 'exists:branches,id'],
            ]);

            // ================= USER =================

            $user->username = $request->username;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // ================= PROFILE =================

            $profile = $user->profile ?? new \App\Models\Profile();
            $profile->user_id = $user->id;

            $profile->nip = $request->nip;
            $profile->nik = $request->nik;
            $profile->nama = $request->nama;
            $profile->alamat = $request->alamat;
            $profile->status = $request->status;
            $profile->no_hp = $request->no_hp;
            $profile->no_rek = $request->no_rek;
            $profile->nama_bank = $request->nama_bank;
            $profile->gaji = $request->gaji;
            $profile->branch_id = $request->branch_id;

            $profile->save();

            DB::commit();

            return back()->with('status', 'edited');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }
}
