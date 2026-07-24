<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tourist;
use App\Models\Otp;
use App\Models\PasswordReset;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([

            'full_name'=>'required|min:5|max:50',

            'email'=>'required|email',

            'password'=>'required|min:8',

            'country'=>'required',

            'phone_number'=>'required'

        ]);
            
        if (Tourist::where('email', $request->email)->exists()) {

            return response()->json([
                'message' => 'Email already registered.'
            ],409);

        }
        
        $tourist = Tourist::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'password' => bcrypt($request->password),
        ]);

        $otpCode = rand(100000, 999999);

        Otp::create([
            'touristID' => $tourist->touristID,
            'otp_code' => $otpCode,
            'expiry_time' => now()->addMinutes(10),
            'status' => 'pending',
        ]);

        Mail::to($tourist->email)->send(new OtpMail($otpCode));
        \App\Models\Email::create([
            'complaintID' => null,
            'recipient_email' => $tourist->email,
            'subject' => 'Your OTP Code',
            'sent_status' => 'Sent',
            'email_type' => 'OTP Verification',
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => 'Registration successful. OTP sent to email.',
            'touristID' => $tourist->touristID
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $otpRecord = Otp::where('touristID', $request->touristID)
            ->where('otp_code', $request->otp_code)
            ->where('status', 'pending')
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (now()->gt($otpRecord->expiry_time)) {
            return response()->json([
                'message' => 'OTP Expired'
            ], 400);
        }

        $otpRecord->status = 'verified';
        $otpRecord->save();

        Tourist::where('touristID', $request->touristID)
            ->update(['is_verified' => true]);

        return response()->json([
            'message' => 'OTP Verified Successfully'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $tourist = Tourist::find($request->touristID);

        if (!$tourist) {
            return response()->json([
                'message' => 'Tourist not found'
            ], 404);
        }

        Otp::where('touristID', $tourist->touristID)
            ->where('status', 'pending')
            ->delete();

        $otpCode = rand(100000, 999999);

        Otp::create([
            'touristID' => $tourist->touristID,
            'otp_code' => $otpCode,
            'expiry_time' => now()->addMinutes(10),
            'status' => 'pending',
        ]);

        Mail::to($tourist->email)->send(new OtpMail($otpCode));
        \App\Models\Email::create([
            'complaintID' => null,
            'recipient_email' => $tourist->email,
            'subject' => 'Your OTP Code',
            'sent_status' => 'Sent',
            'email_type' => 'OTP Verification',
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => 'New OTP sent successfully'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $tourist = Tourist::where('email', $request->email)->first();

        if (!$tourist || !Hash::check($request->password, $tourist->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$tourist->is_verified) {
            return response()->json([
                'message' => 'Please verify your email first'
            ], 403);
        }

        $tourist->tokens()->delete();

        $token = $tourist->createToken('tourist-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'tourist' => $tourist
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $tourist = Tourist::where('email', $request->email)->first();

        if (!$tourist) {
            return response()->json([
                'message' => 'Email not found'
            ],404);
        }

        PasswordReset::where('touristID', $tourist->touristID)
            ->where('status','pending')
            ->delete();

        $otpCode = rand(100000,999999);

        PasswordReset::create([
            'touristID' => $tourist->touristID,
            'otp_code' => $otpCode,
            'expiry_time' => now()->addMinutes(10),
            'status' => 'pending'
        ]);

        Mail::to($tourist->email)->send(new OtpMail($otpCode));
        \App\Models\Email::create([
            'complaintID' => null,
            'recipient_email' => $tourist->email,
            'subject' => 'Your OTP Code',
            'sent_status' => 'Sent',
            'email_type' => 'OTP Verification',
            'sent_at' => now(),
        ]);

        return response()->json([
            'message'=>'Password reset OTP sent.',
            'touristID'=>$tourist->touristID
        ]);
    }

    public function verifyResetOtp(Request $request)
    {
        $reset = PasswordReset::where('touristID',$request->touristID)
            ->where('otp_code',$request->otp_code)
            ->where('status','pending')
            ->first();

        if(!$reset){
            return response()->json([
                'message'=>'Invalid OTP'
            ],400);
        }

        if(now()->gt($reset->expiry_time)){
            return response()->json([
                'message'=>'OTP Expired'
            ],400);
        }

        $reset->status='verified';
        $reset->save();

        return response()->json([
            'message'=>'OTP verified'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'touristID'=>'required',
            'password'=>'required|min:8'
        ]);

        $verifiedOtp = PasswordReset::where('touristID',$request->touristID)
            ->where('status','verified')
            ->latest()
            ->first();

        if(!$verifiedOtp){
            return response()->json([
                'message'=>'OTP not verified.'
            ],400);
        }

        $tourist = Tourist::where('touristID',$request->touristID)->first();
        if($tourist) {
            $tourist->update([
                'password'=>bcrypt($request->password)
            ]);

            // Create notification
            \App\Models\TouristNotification::create([
                'touristID' => $tourist->touristID,
                'title' => 'Password Updated',
                'message' => 'Your account password has been successfully reset. If you did not make this change, please contact support immediately.',
                'type' => 'security',
                'is_read' => false,
            ]);

            // Send Email
            try {
                $subject = "TCMS Security: Password Updated Successfully";
                \Illuminate\Support\Facades\Mail::send('emails.password_updated', [
                    'user' => $tourist
                ], function ($message) use ($tourist, $subject) {
                    $message->to($tourist->email)
                            ->subject($subject);
                });

                \App\Models\Email::create([
                    'recipient_email' => $tourist->email,
                    'subject' => $subject,
                    'sent_status' => 'Sent',
                        'sent_at' => now(),
                    'email_type' => 'Security Alert',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Password reset email failed: ' . $e->getMessage());
                \App\Models\Email::create([
                    'recipient_email' => $tourist->email ?? '',
                    'subject' => "TCMS Security: Password Updated Successfully",
                    'sent_status' => 'Failed',
                        'sent_at' => null,
                    'email_type' => 'Security Alert',
                ]);
            }
        }

        PasswordReset::where('touristID',$request->touristID)->delete();

        return response()->json([
            'message'=>'Password updated successfully'
        ]);
    }

}