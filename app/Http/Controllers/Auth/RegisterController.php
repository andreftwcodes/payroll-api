<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    public function action(RegisterRequest $request)
    {
        $user = User::create(
            $request->only('email', 'name', 'password')
        );

        return new UserResource($user);
    }
}
