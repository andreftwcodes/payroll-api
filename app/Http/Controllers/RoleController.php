<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\Role\RoleResource;

class RoleController extends Controller
{
    public function index()
    {
        return RoleResource::collection(
            Role::all()
        );
    }

    public function store(Request $request, Role $role)
    {
        return new RoleResource(
            $role->addRole($request->only('name'))
        );
    }
}
