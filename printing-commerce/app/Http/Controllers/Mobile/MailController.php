<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Auth;
use App\Models\VerifikasiUser;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MailController extends Controller
{
    /**
     * Send email for password reset
     */
    public function createForgotPassword(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }

        // Check if email exists
        $auth = Auth::where('email', $request->input('email'))->first();
        if (!$auth) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 404);
        }

        // Generate OTP code
        $otp = rand(100000, 999999);
        
        // Generate reset link
        $resetLink = Str::random(64);
        
        // Save verification data
        $user = User::where('id_auth', $auth->id_auth)->first();
        
        // Delete any existing verification requests
        VerifikasiUser::where('email', $request->input('email'))
            ->where('deskripsi', 'forgot_password')
            ->delete();
            
        // Create new verification
        $verification = new VerifikasiUser();
        $verification->email = $request->input('email');
        $verification->kode_otp = $otp;
        $verification->link_otp = $resetLink;
        $verification->deskripsi = 'forgot_password';
        $verification->terkirim = false;
        $verification->id_user = $user->id_user;
        $verification->save();
        
        // Send email with OTP code (in production)
        // This is where you would send the actual email
        // Mail::to($request->input('email'))->send(new \App\Mail\ForgotPasswordMail($otp, $user->nama_user));
        
        // For development, just return the OTP
        return response()->json([
            'status' => 'success', 
            'message' => 'Kode OTP telah dikirim ke email Anda', 
            'data' => [
                'otp' => $otp, // Remove this in production
                'waktu' => Carbon::now()->addMinutes(5), // OTP valid for 5 minutes
                'reset_link' => url('verify/password/' . $resetLink) // Reset link for web users
            ]
        ]);
    }
    
    /**
     * Send email for email verification
     */
    public function createVerifyEmail(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }

        // Check if email exists
        $auth = Auth::where('email', $request->input('email'))->first();
        if (!$auth) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 404);
        }

        // Generate OTP code
        $otp = rand(100000, 999999);
        
        // Generate verification link
        $verificationLink = Str::random(64);
        
        // Save verification data
        $user = User::where('id_auth', $auth->id_auth)->first();
        
        // Delete any existing verification requests
        VerifikasiUser::where('email', $request->input('email'))
            ->where('deskripsi', 'verify_email')
            ->delete();
            
        // Create new verification
        $verification = new VerifikasiUser();
        $verification->email = $request->input('email');
        $verification->kode_otp = $otp;
        $verification->link_otp = $verificationLink;
        $verification->deskripsi = 'verify_email';
        $verification->terkirim = false;
        $verification->id_user = $user->id_user;
        $verification->save();
        
        // Send email with OTP code (in production)
        // This is where you would send the actual email
        // Mail::to($request->input('email'))->send(new \App\Mail\EmailVerificationMail($otp, $user->nama_user));
        
        // For development, just return the OTP
        return response()->json([
            'status' => 'success', 
            'message' => 'Kode OTP telah dikirim ke email Anda', 
            'data' => [
                'otp' => $otp, // Remove this in production
                'waktu' => Carbon::now()->addMinutes(5), // OTP valid for 5 minutes
                'verification_link' => url('verify/email/' . $verificationLink) // Verification link for web users
            ]
        ]);
    }
} 