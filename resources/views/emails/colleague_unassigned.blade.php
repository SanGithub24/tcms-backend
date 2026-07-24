@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #b91c1c; font-size: 21px; border-bottom: 2px solid #ef4444; padding-bottom: 10px;">
        Colleague Unassigned from Complaint
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello Officer {{ $remainingOfficer->full_name }},</p>
        <p style="margin: 0 0 15px 0;">Officer <strong>{{ $removedOfficer->full_name }}</strong> has been unassigned from tourist complaint <strong>TCMS-{{ $complaint->complaintID }}</strong>.</p>

        <div style="background-color: #fef2f2; padding: 15px; border-left: 4px solid #ef4444; margin: 15px 0;">
            <p style="margin: 0; font-size: 15px; color: #7f1d1d;"><strong>Reason for their removal:</strong></p>
            <p style="margin: 5px 0 0 0; color: #991b1b;">{{ $removal_reason ?? 'No specific reason provided.' }}</p>
        </div>

        <p style="margin: 0 0 15px 0;">Please continue your investigation of this complaint as assigned. Thank you.</p>
    </div>
@endsection
