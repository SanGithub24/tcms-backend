@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        Complaint Status Update
    </h2>

    <p style="font-size: 15px;">
        Dear <strong>{{ $complaint->tourist->full_name }}</strong>,
    </p>
    
    <p style="font-size: 15px; line-height: 1.7;">
        Thank you for reaching out to the Sri Lanka Tourist Police. We are writing to provide an update regarding the official complaint you submitted.
    </p>
    
    <div style="background-color: #f9f9f9; border-left: 4px solid #d9534f; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 7px 0;"><strong>Complaint Reference ID:</strong> CMP-{{ $complaint->complaintID }}</p>
        <p style="margin: 7px 0;"><strong>Current Status:</strong> <span style="color: #d9534f; font-weight: bold;">Rejected</span></p>
    </div>

    <p style="font-size: 15px;"><strong>Reason for Rejection:</strong></p>
    <div style="font-style: italic; color: #555; background: #fff5f5; padding: 16px; border-radius: 6px; border: 1px solid #ffd8d8; font-size: 15px; line-height: 1.7;">
        "{{ $reason }}"
    </div>

    <p style="margin-top: 25px; font-size: 14px; color: #555555; line-height: 1.6;">
        If you wish to clarify details, please contact our team at <a href="mailto:tcmsadminofficial@gmail.com" style="color: #0284c7; text-decoration: none;">tcmsadminofficial@gmail.com</a>.
    </p>
@endsection