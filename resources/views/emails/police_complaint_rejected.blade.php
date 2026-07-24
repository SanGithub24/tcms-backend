@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Assigned Complaint Rejected
    </h2>
    <div style="padding: 20px 0;">
        <p style="color: #334155; font-size: 16px;">Hello Officer {{ $officer->full_name }},</p>
        <p style="color: #334155; font-size: 16px;">Please be advised that the complaint <strong>CMP-{{ $complaint->complaintID }}</strong>, which was assigned to you, has been officially rejected by the administration.</p>
        
        <div style="background: #fef2f2; padding: 25px; border-radius: 12px; margin: 25px 0; border: 2px dashed #ef4444;">
            <p style="margin: 0 0 10px 0; font-size: 16px; color: #7f1d1d;"><strong>Reason for Rejection:</strong></p>
            <p style="margin: 0; font-size: 16px; color: #7f1d1d;">{{ $rejectionReason }}</p>
        </div>
        
        <p style="color: #64748b; font-size: 14px;">No further action is required on this complaint from your end.</p>
        
        <p style="color: #334155; font-size: 16px; margin-top: 30px;">
            Regards,<br>
            TCMS Admin<br>
            Sri Lanka Tourist Police
        </p>
    </div>
@endsection
