<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Kirim email verifikasi (queue dihandle oleh ShouldQueue interface)
        $user->notify(new VerifyEmailNotification());

        // Kirim email selamat datang (queue dihandle oleh ShouldQueue interface)
        $user->notify(new WelcomeNotification());

        // Trigger event Registered
        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully. Please check your email for verification.',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Check if user exists and credentials are correct
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credentials do not match',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not verified. Please verify your email before login.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already verified',
            ], 400);
        }

        $user->notify(new VerifyEmailNotification());

        return response()->json([
            'status' => 'success',
            'message' => 'Verification email sent',
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::where('id', $request->route('id'))->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already verified',
            ], 400);
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification link',
            ], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully',
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}