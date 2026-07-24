@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        New Tourist Review Submitted
    </h2>

    <p style="font-size: 15px;">
        Dear Administrator,
    </p>

    <p style="font-size: 15px; line-height: 1.7;">
        A tourist has submitted a new review for a resolved complaint.
        Please review the details below. You can manage this review through
        the TCMS Administrator Dashboard.
    </p>

    <!-- Complaint Information -->
    <div style="background-color: #f0f9ff; border-left: 4px solid #0284c7; padding: 18px; margin: 25px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: #0369a1; font-size: 17px;">
            Review Information
        </h3>

        <p style="margin: 7px 0;"><strong>Review ID:</strong> REV{{ $review->reviewID }}</p>
        <p style="margin: 7px 0;"><strong>Complaint Reference ID:</strong> CMP{{ $complaint->complaintID }}</p>
        <p style="margin: 7px 0;"><strong>Tourist Name:</strong> {{ $tourist->full_name }}</p>
        <p style="margin: 7px 0;"><strong>Tourist Email:</strong> <a href="mailto:{{ $tourist->email }}" style="color: #0284c7; text-decoration: none;">{{ $tourist->email }}</a></p>
        <p style="margin: 7px 0;"><strong>Rating:</strong> {{ $review->rating }} / 5</p>
    </div>

    <!-- Review -->
    <div style="margin: 25px 0;">
        <p style="margin-bottom: 8px; font-size: 15px;">
            <strong>Tourist Review:</strong>
        </p>
        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; border-radius: 6px; font-size: 15px; line-height: 1.7; color: #4b5563;">
            "{{ $review->description }}"
        </div>
    </div>

    <!-- Admin Message -->
    <div style="background-color: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 6px; margin-top: 25px; font-size: 14px; line-height: 1.6; color: #92400e;">
        <strong>Administrator Action:</strong><br>
        Please visit the Reviews section of the TCMS Administrator
        Dashboard to view and manage this review.
    </div>

    <p style="margin-top: 25px; font-size: 14px; color: #555555; line-height: 1.6;">
        This review will remain visible in the system unless it is
        rejected by an administrator.
    </p>
@endsection