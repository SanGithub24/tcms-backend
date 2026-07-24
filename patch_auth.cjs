const fs = require('fs');
const path = require('path');

const filePath = 'g:/Sanvarie/Education/UOM BIT/Project/project-tcms/TCMS_Project/backend/app/Http/Controllers/Api/AuthController.php';
let content = fs.readFileSync(filePath, 'utf8');

// Replace Mail::to($tourist->email)->send(new OtpMail($otpCode));
// with Mail::to... and Email::create
const regex = /Mail::to\(\$tourist->email\)->send\(new OtpMail\(\$otpCode\)\);/g;

content = content.replace(regex, `Mail::to($tourist->email)->send(new OtpMail($otpCode));
        \\App\\Models\\Email::create([
            'complaintID' => null,
            'recipient_email' => $tourist->email,
            'subject' => 'Your OTP Code',
            'sent_status' => 'Sent',
            'email_type' => 'OTP Verification',
            'sent_at' => now(),
        ]);`);

fs.writeFileSync(filePath, content);
console.log('Fixed AuthController.php');
