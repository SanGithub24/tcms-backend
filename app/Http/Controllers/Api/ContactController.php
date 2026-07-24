<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|min:3|max:100',

            'email' => 'required|email|max:100',

            'country' => 'required|string|max:100',

            'phone_number' => [
                'required',
                'regex:/^\+?[0-9\s\-]{8,20}$/'
            ],

            'subject' => 'required|string|min:5|max:150',

            'message' => 'required|string|min:10|max:1000',
        ]);

        Mail::to('tcmsadminofficial@gmail.com')
            ->send(new ContactMessageMail($request));

        return response()->json([
            'message' => 'Message sent successfully'
        ]);
    }
}