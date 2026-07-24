@extends('emails.layout')

@section('content')
    <h2 style="margin-top: 0; color: #b91c1c; font-size: 21px; border-bottom: 2px solid #ef4444; padding-bottom: 10px;">
        Notice Rejected
    </h2>
    <div style="padding: 20px 0;">
        <p style="margin: 0 0 15px 0;">Hello Officer {{ $user->full_name }},</p>
        <p style="margin: 0 0 15px 0;">We are writing to inform you that the notice you submitted has been <strong>rejected by the administrator</strong>.</p>
        
        <div style="background-color: #f8fafc; padding: 15px; border-left: 4px solid #94a3b8; margin: 15px 0;">
            <p style="margin: 0; font-size: 15px; color: #334155;"><strong>Notice Title:</strong></p>
            <p style="margin: 5px 0 0 0; color: #475569;">{{ $notice->title }}</p>
        </div>

        <p style="margin: 15px 0 0 0;">If you need more information, please send an email to <a href="mailto:tcmsadminofficial@gmail.com" style="color: #0284c7; text-decoration: underline;">tcmsadminofficial@gmail.com</a>.</p>
    </div>
@endsection
