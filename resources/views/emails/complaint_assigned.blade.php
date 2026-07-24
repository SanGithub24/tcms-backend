@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        New Complaint Assigned
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello Officer {{ $user->full_name }},</p>
        <p style="margin: 0 0 15px 0;">You have been assigned to tourist complaint (Reference: <strong>TCMS-{{ $complaint->complaintID }}</strong>).</p>
        
        <div style="background-color: #f1f5f9; padding: 15px; border-left: 4px solid #0284c7; margin: 15px 0;">
            <p style="margin: 0; font-size: 15px; color: #334155;"><strong>Assignment Reason:</strong></p>
            <p style="margin: 5px 0 0 0; color: #475569;">{{ $assignment_reason ?? 'No specific reason provided.' }}</p>
        </div>

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e2e8f0;">
            <p style="margin: 0; color: #334155;"><strong>Category:</strong> {{ $complaint->category }}</p>
            <p style="margin: 5px 0 0 0; color: #334155;"><strong>Date Submitted:</strong> {{ $complaint->complaint_date }}</p>
        </div>
        <p style="margin: 0 0 15px 0;">You now have access to review and manage this complaint in your TCMS dashboard. Please log in to take necessary actions.</p>
    </div>
@endsection
