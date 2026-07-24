const fs = require('fs');
const path = require('path');

const filePath = 'g:/Sanvarie/Education/UOM BIT/Project/project-tcms/TCMS_Project/backend/app/Http/Controllers/Api/ContactController.php';
let content = fs.readFileSync(filePath, 'utf8');

const regex = /Mail::to\('tcmsadminofficial@gmail\.com'\)\n\s*->send\(new ContactUsMail\(\$validated\)\);/g;

content = content.replace(regex, `Mail::to('tcmsadminofficial@gmail.com')
            ->send(new ContactUsMail($validated));
            
        \\App\\Models\\Email::create([
            'complaintID' => null,
            'recipient_email' => 'tcmsadminofficial@gmail.com',
            'subject' => 'New Contact Us Message',
            'sent_status' => 'Sent',
            'email_type' => 'Contact Us',
            'sent_at' => now(),
        ]);`);

fs.writeFileSync(filePath, content);
console.log('Fixed ContactController.php');
