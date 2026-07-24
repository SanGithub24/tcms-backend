const fs = require('fs');
const path = require('path');

const dir = 'g:/Sanvarie/Education/UOM BIT/Project/project-tcms/TCMS_Project/backend/app/Http/Controllers/Api';
const files = fs.readdirSync(dir).filter(f => f.endsWith('.php'));

files.forEach(file => {
  const filePath = path.join(dir, file);
  let content = fs.readFileSync(filePath, 'utf8');
  
  content = content.replace(/'sent_at'\s*=>\s*now\(\),\s*'sent_at'\s*=>\s*now\(\),/g, "'sent_at' => now(),");
  content = content.replace(/'sent_at'\s*=>\s*null,\s*'sent_at'\s*=>\s*null,/g, "'sent_at' => null,");
  
  // Also fix any duplicate complaintID if it happened
  content = content.replace(/'complaintID'\s*=>\s*\$complaint->complaintID,\s*'complaintID'\s*=>\s*\$complaint->complaintID \?\? null,/g, "'complaintID' => $complaint->complaintID,");
  content = content.replace(/'complaintID'\s*=>\s*\$complaint->complaintID \?\? null,\s*'complaintID'\s*=>\s*\$complaint->complaintID,/g, "'complaintID' => $complaint->complaintID,");

  fs.writeFileSync(filePath, content);
  console.log('Fixed ' + file);
});
