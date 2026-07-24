@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #b91c1c; font-size: 21px; border-bottom: 2px solid #ef4444; padding-bottom: 10px;">
        Complaint Cancelled by Tourist
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello Officer {{ $officer->full_name }},</p>
        <p style="margin: 0 0 15px 0;">We are writing to inform you that tourist complaint <strong>TCMS-{{ $complaintID }}</strong> has been cancelled and permanently deleted by the tourist.</p>

        <div style="background-color: #fef2f2; padding: 15px; border-left: 4px solid #ef4444; margin: 15px 0;">
            <p style="margin: 0; font-size: 15px; color: #7f1d1d;"><strong>Action Required: None</strong></p>
            <p style="margin: 5px 0 0 0; color: #991b1b;">The tourist exercised their right to delete the complaint within the 15-minute grace period after submission. The complaint and all related evidence have been removed from the system. You do not need to take any further action regarding this case.</p>
        </div>

        <p style="margin: 0 0 15px 0;">Thank you for your attention to this matter.</p>
    </div>
@endsection
