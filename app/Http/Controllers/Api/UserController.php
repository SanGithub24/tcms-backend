<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Email;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function registerUser(Request $request)
    {
        $validated = $request->validate([
            'user_type' => 'required|in:admin,police',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required',
            'badge_number' => 'required|unique:users,badge_number',
            'working_station' => 'required|string|max:255',
            'status' => 'nullable|in:Active,Inactive',
            'district' => 'required|string',
        ]);

        
        $plainPassword = 'P0L#' . rand(1000, 9999);

        $user = User::create([
            'user_type' => strtolower($validated['user_type']),
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'badge_number' => $validated['badge_number'],
            'district' => $validated['district'],
            'working_station' => $validated['working_station'],
            'status' => $validated['status'] ?? 'Active',
            'password' => bcrypt($plainPassword),
        ]);

        
        $subject = "TCMS Police Officer Account Created";

        try {
            Mail::send('emails.officer_created', [
                'user' => $user,
                'plainPassword' => $plainPassword
            ], function ($message) use ($user, $subject) {
                $message->to($user->email)
                        ->subject($subject);
            });

            Email::create([
                'recipient_email' => $user->email,
                'subject' => $subject,
                'sent_status' => 'Sent',
                        'sent_at' => now(),
                'email_type' => 'Officer Registration',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Email::create([
                'recipient_email' => $user->email,
                'subject' => $subject,
                'sent_status' => 'Failed',
                        'sent_at' => null,
                'email_type' => 'Officer Registration',
            ]);
        }

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function loginUser(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Incorrect password'
            ], 401);
        }

        if (strtolower($user->status) !== 'active') {
            return response()->json([
                'message' => 'This account has been deactivated. Please contact an administrative officer or send an email to tcmsadminofficial@gmail.com'
            ], 403);
        }

        $token = $user->createToken('admin-police-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function updatePolicePassword(Request $request)
    {
        $request->validate([
            'userID' => 'required|exists:users,userID',
            'current_password' => 'required',
            'new_password' => 'required|min:8'
        ]);

        $user = User::find($request->userID);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Your current password is incorrect.'
            ], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully!'
        ], 200);
    }


    public function updateTourist(Request $request, int $id)
    {
        $tourist = Tourist::find($id);

        if (!$tourist) {
            return response()->json([
                'message' => 'Tourist not found'
            ], 404);
        }

        $passwordChanged = false;

        if ($request->has('current_password') && $request->filled('current_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $tourist->password)) {
                return response()->json([
                    'message' => 'Your current password is incorrect.'
                ], 400);
            }
            if ($request->filled('new_password')) {
                $tourist->password = bcrypt($request->new_password);
                $passwordChanged = true;
            }
        }

        $tourist->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone,
            'country' => $request->country,
        ]);

        if ($passwordChanged) {
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

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $tourist
        ]);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id . ',userID',
            'badge_number' => 'required|unique:users,badge_number,' . $id . ',userID',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required',
            'district' => 'required|string',
            'working_station' => 'required|string|max:255',
        ]);

        $wasInactive = (strtolower($user->status) === 'inactive' || strtolower($user->status) === 'deactivated');
        $becomingActive = ($request->has('status') && strtolower($request->status) === 'active');

        $user->fill($request->all());

        if ($wasInactive && $becomingActive) {
            $plainPassword = 'P0L#' . rand(1000, 9999);
            $user->password = bcrypt($plainPassword);
            
            $subject = "TCMS Police Officer Account Reassigned & Reactivated";

            try {
                Mail::send('emails.officer_reassigned', [
                    'user' => $user,
                    'plainPassword' => $plainPassword
                ], function ($message) use ($user, $subject) {
                    $message->to($user->email)
                            ->subject($subject);
                });

                \App\Models\Email::create([
                    'recipient_email' => $user->email,
                    'subject' => $subject,
                    'sent_status' => 'Sent',
                        'sent_at' => now(),
                    'email_type' => 'Officer Reassignment',
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                \App\Models\Email::create([
                    'recipient_email' => $user->email,
                    'subject' => $subject,
                    'sent_status' => 'Failed',
                        'sent_at' => null,
                    'email_type' => 'Officer Reassignment',
                ]);
            }
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function getAllTourists()
    {
        try {
            
            $tourists = DB::table('tourists')
                ->select('touristID', 'full_name', 'email', 'phone_number', 'country', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'tourists' => $tourists
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tourists.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTourist($id)
    {
        try {
            $deleted = DB::table('tourists')->where('touristID', $id)->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tourist record not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tourist account deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tourist record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}