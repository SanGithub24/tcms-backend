@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Additional Officer Assigned to Complaint
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello Officer {{ $user->full_name }},</p>
        <p style="margin: 0 0 15px 0;">Please note that an additional officer (<strong>{{ $newOfficer->full_name }}</strong>) has also been assigned to tourist complaint <strong>TCMS-{{ $complaint->complaintID }}</strong>.</p>

        <div style="background-color: #f1f5f9; padding: 15px; border-left: 4px solid #f59e0b; margin: 15px 0;">
            <p style="margin: 0; font-size: 15px; color: #334155;"><strong>Reason for additional assignment:</strong></p>
            <p style="margin: 5px 0 0 0; color: #475569;">{{ $assignment_reason ?? 'No specific reason provided.' }}</p>
        </div>

        <p style="margin: 0 0 15px 0;">You remain assigned to this case. Both you and the newly assigned officer have full access to review and manage this complaint in the TCMS dashboard. You can collaborate to resolve the issue.</p>
    </div>
@endsection
