<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\Permission\PermissionRequest;
use App\Http\Resources\PermissionResource;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::all();
    }

    public function store(PermissionRequest $request)
    {
        
        $permission = Permission::create($request->validated());

        return new PermissionResource($permission);
    }

    public function destroy($id)
    {
        Permission::destroy($id);
        return new PermissionResource($id);
    }
}
