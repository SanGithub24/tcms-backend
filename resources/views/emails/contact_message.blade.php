@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #0369a1; font-size: 21px; border-bottom: 2px solid #0284c7; padding-bottom: 10px;">
        New Tourist Contact Request
    </h2>

    <p style="font-size: 15px;">
        Dear Administrator,
    </p>

    <p style="font-size: 15px; line-height: 1.7;">
        You have received a new contact message from a tourist. Please review the details below.
    </p>

    <div style="background-color: #f0f9ff; border-left: 4px solid #0284c7; padding: 18px; margin: 25px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: #0369a1; font-size: 17px;">
            Contact Details
        </h3>
        <p style="margin: 7px 0;"><strong>Full Name:</strong> {{ $data->full_name }}</p>
        <p style="margin: 7px 0;"><strong>Email:</strong> <a href="mailto:{{ $data->email }}" style="color: #0284c7; text-decoration: none;">{{ $data->email }}</a></p>
        <p style="margin: 7px 0;"><strong>Country:</strong> {{ $data->country }}</p>
        <p style="margin: 7px 0;"><strong>Phone Number:</strong> {{ $data->phone_number }}</p>
        <p style="margin: 7px 0;"><strong>Subject:</strong> {{ $data->subject }}</p>
    </div>

    <div style="margin: 25px 0;">
        <p style="margin-bottom: 8px; font-size: 15px;">
            <strong>Message:</strong>
        </p>
        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; border-radius: 6px; font-size: 15px; line-height: 1.7; color: #4b5563;">
            {{ $data->message }}
        </div>
    </div>
@endsection
