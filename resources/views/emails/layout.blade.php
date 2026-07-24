<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TCMS Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fb; font-family: Arial, Helvetica, sans-serif; color: #333333;">

    <div style="max-width: 650px; margin: 30px auto; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #0f172a, #0ea5e9); padding: 25px 30px; text-align: center;">
            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="TCMS Logo" style="height: 60px; margin-bottom: 15px;">
            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">
                Tourist Complaint Management System
            </h1>
            <p style="margin: 8px 0 0; color: #e0f2fe; font-size: 14px;">
                Sri Lanka Tourist Police
            </p>
        </div>

        <!-- Main Content -->
        <div style="padding: 30px;">
            @yield('content')
        </div>

        <!-- Footer -->
        <div style="background-color: #f8fafc; border-top: 1px solid #e5e7eb; padding: 20px 30px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #777777; line-height: 1.6;">
                This is an automated notification from the Sri Lanka Tourist Police Dashboard.<br>
                Please do not reply to this email.
            </p>
        </div>

    </div>

</body>
</html>
