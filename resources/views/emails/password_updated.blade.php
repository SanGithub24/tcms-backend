@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Password Updated Successfully
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello {{ $user->full_name }},</p>
        <p style="margin: 0 0 15px 0;">This email is to confirm that the password for your TCMS account has been successfully updated.</p>
        
        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <p style="margin: 0 0 10px 0; color: #64748b; font-size: 14px;"><strong>Account Details:</strong></p>
            <p style="margin: 0 0 5px 0;"><strong>Name:</strong> {{ $user->full_name }}</p>
            <p style="margin: 0 0 5px 0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 0 0 5px 0;"><strong>Time of Change:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
        
        <p style="margin: 0 0 15px 0; color: #ef4444; font-weight: bold;">If you did not make this change, please contact our support team immediately to secure your account.</p>
        <p style="margin: 0 0 15px 0;">You can now log in to the TCMS application using your new password.</p>
    </div>
@endsection
