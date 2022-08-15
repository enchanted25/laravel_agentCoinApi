<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller


{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|String',
            'email' => 'required|String|unique:users,email',
            'password' => 'required|String|confirmed',
            'role' => 'required|String|unique:users,role',
            'balance' => 'required|integer'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'],
            'balance' => $fields['balance']

        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([

            'email' => 'required|String',
            'password' => 'required|String'

        ]);

        //check email
        $user = User::where('email', $fields['email'])->first();

        //check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'wrong email or password'
            ], 401);
        }

        $token = $user->createToken('masteragentToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function show($id)
    {
        return User::find($id);
    }


    public function logout(User $user)
    {
        $user->tokens()->delete();
        return [
            'message' => 'Logged Out'
        ];
    }

    // public function destroy($id)
    // {
    //     return User::destroy($id);
    // }
}
