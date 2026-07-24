@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Complaint Submitted Successfully
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello {{ $user->full_name }},</p>
        <p style="margin: 0 0 15px 0;">Your complaint (Reference: <strong>CMP{{ $complaint->complaintID }}</strong>) has been successfully submitted to the TCMS system.</p>
        <p style="margin: 0 0 15px 0;">Our team will review your complaint and an assigned police officer will look into it shortly. You can track the status of your complaint at any time through your dashboard.</p>
        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e2e8f0;">
            <p style="margin: 0; color: #334155;"><strong>Date Submitted:</strong> {{ $complaint->complaint_date }}</p>
            <p style="margin: 5px 0 0 0; color: #334155;"><strong>Category:</strong> {{ $complaint->category }}</p>
        </div>
        <p style="margin: 0 0 15px 0;">Thank you for helping us keep Sri Lanka safe.</p>
    </div>
@endsection
