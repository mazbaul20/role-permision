<?php

namespace App\Http\Controllers;

use view;
use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\RoleFormRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $roles = Role::with('Permissions')->latest()->get();
        return view('role.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        // $permission_groups = User::getPermissionGroup();
        return view('role.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // CreateRole::create($request);
        $request->validate([
            "name" => "required|unique:roles,name",
        ]);
        $role = Role::create(['name'=>$request->name,'guard_name'=>'web']);
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Role Created!');
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permissions = Permission::all();
        $role = Role::with('permissions')->find($id);
        $data = $role->permissions()->pluck('id')->toArray();
        
        // $permission_groups = User::getPermissionGroup();
        return view('role.edit',compact(['permissions','role','data']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            "name" => "required|unique:roles,name,$role->id",
        ]);
        // return $request->all();
        $role->update(['name'=>$request->name]);
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Role has been Updated successful!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(role $role)
    {
        // abort_if(!userCan('role.delete'), 403);
        $role->delete();
        session()->flash('success', 'Role Deleted successfully!');
        return redirect()->back();
        
    }
}
