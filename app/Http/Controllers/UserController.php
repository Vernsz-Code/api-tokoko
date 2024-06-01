<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(Request $request)
    {
        $phone_number = $request->input('phone_number');
        $email = $request->input('email');

        // Validate based on whether email or phone number is provided
        if (is_null($phone_number)) {
            $validated = $request->validate([
                'username' => 'required',
                'firstname' => 'required',
                'lastname' => '', // lastname is not required
                'email' => 'required|email',
                'password' => 'required|min:6',
                'user_uuid' => 'required',
            ], [
                'username.required' => 'The username field is required.',
                'firstname.required' => 'The firstname field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least :min characters.',
                'user_uuid.required' => 'The user_uuid field is required.',
            ]);
        } elseif (is_null($email)) {
            $validated = $request->validate([
                'username' => 'required',
                'firstname' => 'required',
                'lastname' => '', // lastname is not required
                'phone_number' => 'required',
                'password' => 'required|min:6',
                'user_uuid' => 'required',
            ], [
                'username.required' => 'The username field is required.',
                'firstname.required' => 'The firstname field is required.',
                'phone_number.required' => 'The phone number field is required.',
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least :min characters.',
                'user_uuid.required' => 'The user_uuid field is required.',
            ]);

            if (substr($phone_number, 0, 1) === '0') {
                $validated['phone_number'] = '62' . ltrim($phone_number, '0');
            }
        } else {
            // Handle the case where both email and phone number are provided
            return response()->json(['message' => 'Please provide either email or phone number, not both.'], 400);
        }

        // Hash the user_uuid before storing it
        $validated['user_uuid'] = Hash::make($validated['user_uuid']);

        try {
            $validated['lastname'] = $request->input('lastname');
        
            $user = User::create($validated);
        
            return response()->json(['message' => 'User created successfully', 'data' => new UserResource($user)], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating user', 'error' => $e->getMessage()], 500);
        }
        
    }



    public function getuser(Request $request)
    {
        $email = $request->input('email');
        $phone_number = $request->input('phone_number');
        $user = null;

        $formatted_phone_number = preg_replace('/^0/', '62', $phone_number);

        if (is_null($phone_number)) {
            $user = User::where('email', $email)->first();
        }

        if (is_null($email)) {
            $user = User::where('phone_number', $formatted_phone_number)->first();
        }

        if (is_null($email) && is_null($phone_number)) {
            return response()->json(['message' => 'Email or phone_number is required.', 'error' => 'missing_parameters'], 400);
        }

        if ($user) {
            return response()->json(['message' => 'User found', 'data' => [
                'username' => $user->username,
                'phone_number' => $user->phone_number
            ]], 200);
        } else {
            return response()->json(['message' => 'User not found.', 'error' => 'user_not_found'], 404);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken($email)->plainTextToken;
            return response()->json(['message' => 'Login successful', 'id' => $user->id, 'token' => $token], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}
