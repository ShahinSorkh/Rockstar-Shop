<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::query()
            ->where('email', $request->email)
            ->first();

        if ($user === null || !Hash::check($request->password, $user->password)) {
            throw new AuthenticationException();
        }

        return ['api_token' => $user->api_token];
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(16),
        ]);

        event(new Registered($user));

        return ['api_token' => $user->api_token];
    }

    public function revokeToken(Request $request)
    {
        $user = $request->user();
        $user->update([
            'api_token' => Str::random(16),
        ]);

        return ['api_token' => $user->api_token];
    }

    public function whoami(Request $request)
    {
        return $request->user();
    }
}
