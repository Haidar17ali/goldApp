<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public function index(){
        $roles = Role::orderBy("id", "desc")->paginate(10);
        return view('pages.roles.index', compact(['roles']));
    }

    public function create(){
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('pages.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request){
        // validasi
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions.*' => 'required|exists:permissions,id'
        ]);

        // ambil data permission berdasarkan permission yang dipilih
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        
        // cek apakah permission ada atau tidak
        if(!count($permissions)){
            // jika tidak kembalikan ke halaman sebelumnya dengan status permission tidak ada
            return redirect()->back()->with('status', 'none');
        }

        $role = Role::create(['name' => $request->name]);

        // input data permission ke role
        $role->syncPermissions($permissions);
        return redirect()->route('role.index')->with('status', 'saved');
    }

    public function edit($id) {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });
        return view('pages.roles.edit', compact(['role', 'groupedPermissions']));
    }

    public function update(Request $request, $id){
        $role = Role::with('permissions')->findOrFail($id);
        
        // validasi
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions.*' => 'required|exists:permissions,id'
        ]);

        // cek apakah permissions ada atau tidak
        if(count($role->permissions)){
            $revokePermissionId = [];
            // ambil data permission_id untuk di hapus dari role
            foreach($role->permissions as $permission){
                $revokePermissionId[] = $permission->id;
            }
            // hapus data permission yang lama
            $role->revokePermissionTo($revokePermissionId);
        }
        // hapus data permission yang lama
        $role->revokePermissionTo($revokePermissionId);
        
        // ambil data permission berdasarkan permission yang dipilih
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        // cek apakah permission ada atau tidak
        if(!count($permissions)){
            // jika tidak kembalikan ke halaman sebelumnya dengan status permission tidak ada
            return redirect()->back()->with('status', 'none');
        }

        $role->name = $request->name;
        
        // input data permission ke role
        $role->syncPermissions($permissions);
        $role->save();
        return redirect()->route('role.index')->with('status', 'edited');
    }

    public function destroy($id){
        $role = Role::with(['permissions', 'users'])->findOrFail($id);

        // cek apakah user ada atau tidak
        if(count($role->users)){
            // hapus role dari setiap pemgguna yang memiliki role yang akan dihapus
            foreach ($role->users as $user) {
                $user->removeRole($role);
            }
        }
        
        // cek apakah permissions ada atau tidak
        if(count($role->permissions)){
            $revokePermissionId = [];
            // ambil data permission_id untuk di hapus dari role
            foreach($role->permissions as $permission){
                $revokePermissionId[] = $permission->id;
            }
            // hapus data permission yang lama
            $role->revokePermissionTo($revokePermissionId);
        }

        $role->delete();
        return redirect()->route('role.index')->with('status', 'deleted');

    }
}
