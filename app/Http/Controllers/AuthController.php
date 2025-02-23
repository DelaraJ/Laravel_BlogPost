<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function register(RegisterUserRequest $request): JsonResponse
    {        
        $input = $request->all();
        $user = User::create($input);
        $token =  $user->createToken($user->name)->plainTextToken;

        return response()->json(['data' => [ 'token'=> $token]]);
    
    }
    
    public function login(LoginUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->firstOrFail();

        throw_unless(
            Hash::check($request->input('password'), $user->password),
            AuthenticationException::class
        );

        $token =  $user->createToken($user->name)->plainTextToken;
        return response()->json(['data' => ['token' => $token]]);
    }
    

    public function logout(Request $request): void 
    {
        $request->user()->currentAccessToken()->delete();
    }
}
