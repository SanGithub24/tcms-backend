const fs = require('fs');
const path = require('path');

const dir = 'g:/Sanvarie/Education/UOM BIT/Project/project-tcms/TCMS_Project/backend/app/Http/Controllers/Api';
const files = fs.readdirSync(dir).filter(f => f.endsWith('.php'));

files.forEach(file => {
  const filePath = path.join(dir, file);
  let content = fs.readFileSync(filePath, 'utf8');
  
  // For 'sent_status' => 'Sent', we add 'sent_at' => now()
  content = content.replace(/'sent_status'\s*=>\s*'Sent',/g, "'sent_status' => 'Sent',\n                        'sent_at' => now(),");
  
  // For 'sent_status' => 'Failed', we add 'sent_at' => null
  content = content.replace(/'sent_status'\s*=>\s*'Failed',/g, "'sent_status' => 'Failed',\n                        'sent_at' => null,");
  
  if (['ComplaintController.php', 'AssignmentController.php'].includes(file)) {
         content = content.replace(/Email::create\(\[\s*'recipient_email'/g, "Email::create([\n                        'complaintID' => $complaint->complaintID ?? null,\n                        'recipient_email'");
  }

  fs.writeFileSync(filePath, content);
  console.log('Updated ' + file);
});
