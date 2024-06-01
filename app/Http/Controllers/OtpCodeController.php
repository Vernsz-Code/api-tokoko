<?php

namespace App\Http\Controllers;

use App\Models\otp_code;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class OtpCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $phone_number = $request->input('phone_number');
        $otp = $request->input('otp_code');

        $sentOtp = otp_code::where('phone_number', $phone_number)->where('otp_code', $otp)->where('created_at', '>=', now()->subMinutes(5))->first();

        if(!$sentOtp){
            throw ValidationValidationException::withMessages([
                'message' => 'otp code is wrong',
            ]);
        }

        $user = User::where('phone_number', $phone_number)->first();

        if(!$user){
            throw ValidationValidationException::withMessages([
                'message' => 'user not register'
            ]);
        }

        $token = $user->createToken($phone_number)->plainTextToken;
        
        return response()->json(['message' => 'Login successful', 'id' => $user->id, 'token' => $token], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $randomNumber = mt_rand(100000, 999999);
        $randomNumberString = str_pad((string)$randomNumber, 6, '0', STR_PAD_LEFT);
        
        $validate = $request->validate([
            'phone_number' => 'required|min:8',
            'otp_code' => 'required',
        ], [
            'phone_number.required' => 'The phone number field is required',
            'phone_number.min' => 'Phone number must be at least :min characters',
            'otp_code.required' => 'The otp code field is required',
        ]);
        
        $validate['otp_code'] = $randomNumberString;

        try {
            $otp = new otp_code($validate);
            $otp->save();
            
            return response()->json(['message' => 'User created successfully', 'data' => $otp], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating user', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        $verificationCode = otp_code::orderBy('id', 'desc')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();
    
        if ($verificationCode) {
            return response()->json($verificationCode);
        } else {
            return response()->json(['message' => 'No data found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(otp_code $otp_code)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, otp_code $otp_code)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $otp_code = $request->input("otp_code");
        $destroyAll = otp_code::where('created_at', '<=', now()->subMinutes(5));
        $otp = otp_code::where('otp_code', $otp_code)->first();
        if($otp){
            $otp->delete();
            $destroyAll->delete();
            return response()->json(["message" => "data successfully deleted",
            "data" => $otp], 200);
        }
        else{
            return response()->json(["message" => "data doesn't exist", "data" => $request->input("otp_code")], 404);
        }
    }
}
