@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Account Reassigned & Reactivated
    </h2>
    <div style="padding: 20px 0;">
        <p style="color: #334155; font-size: 16px;">Hello Officer {{ $user->full_name }},</p>
        <p style="color: #334155; font-size: 16px;">Your account credentials have been reissued for the Tourist Complaint Management System (TCMS).</p>
        
        <div style="background: #f0f9ff; padding: 25px; border-radius: 12px; margin: 25px 0; border: 2px dashed #0ea5e9;">
            <p style="margin: 0 0 10px 0; font-size: 16px; color: #0f172a;"><strong>Login Credentials:</strong></p>
            <p style="margin: 5px 0; font-size: 16px; color: #0f172a;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 5px 0; font-size: 16px; color: #0f172a;"><strong>Password:</strong> {{ $plainPassword }}</p>
        </div>
        
        <p style="color: #64748b; font-size: 14px;">Please login immediately and change your password through the system settings.</p>
        
        <p style="color: #334155; font-size: 16px; margin-top: 30px;">
            Regards,<br>
            TCMS Admin<br>
            Sri Lanka Tourist Police
        </p>
    </div>
@endsection
