@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0f766e; font-size: 21px; border-bottom: 2px solid #14b8a6; padding-bottom: 10px;">
        Complaint Resolved
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello {{ $user->full_name }},</p>
        <p style="margin: 0 0 15px 0;">We are writing to inform you that your complaint <strong>CMP-{{ $complaint->complaintID }}</strong> has been marked as <strong>Resolved</strong> by the investigating officer.</p>

        @if(!empty($police_note))
        <div style="background-color: #f0fdfa; padding: 15px; border-left: 4px solid #14b8a6; margin: 15px 0;">
            <p style="margin: 0; font-size: 15px; color: #0f766e;"><strong>Investigation Note (Resolution):</strong></p>
            <p style="margin: 5px 0 0 0; color: #115e59; white-space: pre-wrap;">{{ $police_note }}</p>
        </div>
        @endif

        <p style="margin: 0 0 15px 0;">You can view the full details of this resolution and provide your feedback or review by logging into the Tourist Dashboard.</p>
        
        <p style="margin: 0;">Thank you for your cooperation and for using our Tourist Complaint Management System.</p>
    </div>
@endsection
