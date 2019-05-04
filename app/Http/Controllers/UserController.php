<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(
            User::with(['roles'])->get()
        );
    }

    public function store(Request $request, User $user)
    {
        return new UserResource(
            $user->createUser($request)
        );
    }

    public function update(Request $request, User $user)
    {
        return new UserResource(
            $user->updateUser($request)
        );
    }

    public function show(User $user)
    {
        return new UserResource(
            $user->load(['roles'])
        );
    }
}
