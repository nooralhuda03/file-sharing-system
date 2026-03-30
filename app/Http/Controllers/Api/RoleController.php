<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
   
    public function index()
    {
        return Role::all();
    }

  
    public function store(RoleRequest  $request)
    {
        $role = Role::create($request->validated());

    return new RoleResource($role);
    }
    private function find($id)
    {
        return Role::findOrFail($id);
    }

    public function show($id)
    {
        $role = $this->find($id);
    
        return new RoleResource($role);
    }

    public function update(Request $request, $id)
    {
        $role = $this->find($id);

        $role->update([
            'name' => $request->name
        ]);

        return response()->json($role);
    }


    public function destroy($id)
    {
        Role::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
 public function assignPermissions(Request $request, $roleId)
{
    $request->validate([
        'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id'
    ]);

    $role = $this->find($roleId);

    $role->permissions()->syncWithoutDetaching($request->permissions);

    return response()->json(['message' => 'Permissions assigned']);
}
}
