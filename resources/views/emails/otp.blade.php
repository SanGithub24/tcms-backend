@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Account Verification
    </h2>
    <div style="padding: 20px 0; text-align: center;">
        <p style="color: #334155; font-size: 16px;">Please use the following OTP to verify your account:</p>
        <div style="background: #f0f9ff; padding: 25px; border-radius: 12px; margin: 25px 0; border: 2px dashed #0ea5e9;">
            <h1 style="margin: 0; font-size: 42px; color: #0f172a; letter-spacing: 8px;">{{ $otp }}</h1>
        </div>
        <p style="color: #64748b; font-size: 14px;">This code is valid for 10 minutes. If you did not request this, please ignore this email.</p>
    </div>
@endsection
