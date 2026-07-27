<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tourist;
use App\Models\Complaint;
use App\Models\Location;
use App\Models\User;
use App\Models\ComplaintAssignment;
use App\Models\Evidence;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $faker = Faker::create('en_US'); 

        // 1. District and Location Data
        $districtData = [
            'Ampara' => ['province' => 'Eastern', 'cities' => ['Arugam Bay', 'Ampara', 'Kalmunai']],
            'Anuradhapura' => ['province' => 'North Central', 'cities' => ['Anuradhapura City', 'Mihintale', 'Kekirawa']],
            'Badulla' => ['province' => 'Uva', 'cities' => ['Ella', 'Badulla', 'Bandarawela', 'Haputale']],
            'Batticaloa' => ['province' => 'Eastern', 'cities' => ['Batticaloa', 'Kalkudah', 'Pasikudah']],
            'Colombo' => ['province' => 'Western', 'cities' => ['Colombo 01', 'Mount Lavinia', 'Dehiwala', 'Moratuwa']],
            'Galle' => ['province' => 'Southern', 'cities' => ['Galle Fort', 'Hikkaduwa', 'Unawatuna', 'Ahangama']],
            'Gampaha' => ['province' => 'Western', 'cities' => ['Negombo', 'Katunayake', 'Kelaniya', 'Gampaha']],
            'Hambantota' => ['province' => 'Southern', 'cities' => ['Tangalle', 'Hambantota', 'Tissamaharama']],
            'Jaffna' => ['province' => 'Northern', 'cities' => ['Jaffna Town', 'Nallur', 'Point Pedro']],
            'Kalutara' => ['province' => 'Western', 'cities' => ['Panadura', 'Kalutara', 'Beruwala', 'Aluthgama']],
            'Kandy' => ['province' => 'Central', 'cities' => ['Kandy City', 'Peradeniya', 'Katugastota']],
            'Kegalle' => ['province' => 'Sabaragamuwa', 'cities' => ['Kegalle', 'Pinnawala', 'Mawanella']],
            'Kilinochchi' => ['province' => 'Northern', 'cities' => ['Kilinochchi', 'Pallai']],
            'Kurunegala' => ['province' => 'North Western', 'cities' => ['Kurunegala', 'Kuliyapitiya', 'Polgahawela']],
            'Mannar' => ['province' => 'Northern', 'cities' => ['Mannar Island', 'Pesalai']],
            'Matale' => ['province' => 'Central', 'cities' => ['Dambulla', 'Sigiriya', 'Matale']],
            'Matara' => ['province' => 'Southern', 'cities' => ['Mirissa', 'Weligama', 'Matara', 'Dikwella']],
            'Monaragala' => ['province' => 'Uva', 'cities' => ['Monaragala', 'Kataragama', 'Wellawaya']],
            'Mullaitivu' => ['province' => 'Northern', 'cities' => ['Mullaitivu', 'Puthukkudiyiruppu']],
            'Nuwara Eliya' => ['province' => 'Central', 'cities' => ['Nuwara Eliya', 'Hatton', 'Nanu Oya']],
            'Polonnaruwa' => ['province' => 'North Central', 'cities' => ['Polonnaruwa', 'Kaduruwela', 'Hingurakgoda']],
            'Puttalam' => ['province' => 'North Western', 'cities' => ['Kalpitiya', 'Puttalam', 'Chilaw']],
            'Ratnapura' => ['province' => 'Sabaragamuwa', 'cities' => ['Ratnapura', 'Balangoda', 'Pelmadulla']],
            'Trincomalee' => ['province' => 'Eastern', 'cities' => ['Trincomalee', 'Nilaveli', 'Uppuveli']],
            'Vavuniya' => ['province' => 'Northern', 'cities' => ['Vavuniya', 'Cheddikulam']]
        ];

        $districts = array_keys($districtData);
        $targetDistricts = ['Colombo', 'Galle', 'Gampaha'];
        $policeDistricts = array_values(array_diff($districts, $targetDistricts));

        // 2. Categories, Statuses, and English Descriptions
        $categories = ['Harassment or Intimidation', 'Overcharging or Scams', 'Food Quality Issue', 'Lost or Stolen Items', 'Transport Issue', 'Other Issue'];
        $statuses = ['Submitted', 'Assigned', 'Investigating', 'Resolved', 'Rejected'];
        $landmarks = ['Railway Station', 'Bus Stand', 'Beach', 'Temple', 'Market', 'Hotel', 'Main Street'];

        $englishDescriptions = [
            "I was severely overcharged by a local taxi driver. He refused to use the meter and demanded triple the normal amount when we arrived at the hotel.",
            "My wallet was stolen while I was walking through the crowded market area. It contained my identification and around $100 in local currency.",
            "The food served at the restaurant was clearly spoiled and caused severe food poisoning for my entire family. They refused to acknowledge the issue.",
            "We were harassed by a group of individuals near the beach who kept following us and demanding money for taking photos.",
            "The tour guide we hired did not take us to any of the promised locations and abandoned us halfway through the trip.",
            "My hotel room was broken into while we were out for dinner. Some expensive electronics and jewelry were taken from our luggage.",
            "I faced significant intimidation from a local vendor when I refused to buy their completely overpriced souvenirs.",
            "The public bus driver was driving extremely recklessly, endangering all passengers. He almost caused a severe collision.",
        ];

        $englishNotes = [
            "Officer has arrived at the location and began questioning witnesses. The area has been checked for CCTV footage.",
            "We have contacted the relevant parties and issued a strict warning. Further investigation is ongoing.",
            "The stolen items have not been recovered yet, but we have a suspect description. Patrols in the area have been increased.",
            "Case is currently under active review. We are coordinating with the tourist police division to identify the culprits.",
            "The dispute was successfully mediated. The overcharged amount was fully refunded to the tourist.",
        ];

        // 3. Create 7 Police Officers
        $sinhalaFirstNames = ['Kasun', 'Kamal', 'Sunil', 'Amila', 'Dasun', 'Lahiru', 'Chamara', 'Ruwan', 'Gayan', 'Nimal', 'Saman'];
        $sinhalaLastNames = ['Perera', 'Silva', 'Fernando', 'Kumara', 'Bandara', 'Jayawardena', 'Rajapaksha', 'Rathnayake', 'Dissanayake', 'Gunawardena'];

        $badgeCounter = 4;
        $policeOfficers = [];
        $selectedPoliceDistricts = $faker->randomElements($policeDistricts, 7);

        foreach ($selectedPoliceDistricts as $district) {
            $officer = User::create([
                'user_type' => 'police',
                'full_name' => $faker->randomElement($sinhalaFirstNames) . ' ' . $faker->randomElement($sinhalaLastNames),
                'email' => 'tcms' . strtolower($district) . '@gmail.com',
                'phone_number' => '07' . $faker->numberBetween(10000000, 99999999),
                'badge_number' => 'PO' . str_pad($badgeCounter, 3, '0', STR_PAD_LEFT),
                'district' => $district,
                'working_station' => $district . ' Tourist Police Station',
                'status' => 'Active',
                'password' => bcrypt('password123'),
            ]);
            $policeOfficers[$district][] = $officer;
            $badgeCounter++;
        }

        $existingOfficers = User::where('user_type', 'police')->get();
        foreach ($existingOfficers as $officer) {
            if ($officer->district) {
                $policeOfficers[$officer->district][] = $officer;
            }
        }

        // 4. Create 40 Tourists
        $tourists = [];
        for ($i = 0; $i < 40; $i++) {
            $tourists[] = Tourist::create([
                'full_name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => '+' . $faker->numberBetween(1000000000, 9999999999),
                'country' => $faker->country,
                'password' => bcrypt('password123'),
                'is_verified' => 1,
            ]);
        }

        // 5. Create Dummy Evidence Files
        $dummyImageName = 'dummy_image_' . time() . '.png';
        $dummyDocName = 'dummy_doc_' . time() . '.pdf';
        
        Storage::disk('public')->put('evidence/' . $dummyImageName, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
        Storage::disk('public')->put('evidence/' . $dummyDocName, '%PDF-1.4 Dummy PDF Content for Testing');

        // 6. Create 50 Complaints
        for ($i = 0; $i < 50; $i++) {
            $tourist = $faker->randomElement($tourists);
            $district = $faker->randomElement($districts);
            $status = $faker->randomElement($statuses);

            $province = $districtData[$district]['province'];
            $city = $faker->randomElement($districtData[$district]['cities']);

            $location = Location::create([
                'city' => $city,
                'district' => $district,
                'province' => $province,
                'latitude' => $faker->latitude(5, 9),
                'longitude' => $faker->longitude(79, 81),
                'description' => 'Near ' . $city . ' ' . $faker->randomElement($landmarks),
            ]);

            $complaint = Complaint::create([
                'touristID' => $tourist->touristID,
                'locationID' => $location->locationID,
                'category' => $faker->randomElement($categories),
                'description' => $faker->randomElement($englishDescriptions),
                'incident_date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'complaint_date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'status' => $status,
                'contact_method' => 'Email',
                'contact_number' => $tourist->phone_number,
                'police_note' => in_array($status, ['Resolved', 'Investigating']) ? "--- INVESTIGATION NOTE ---\n" . $faker->randomElement($englishNotes) : null,
            ]);

            if (in_array($status, ['Assigned', 'Investigating', 'Resolved'])) {
                if (isset($policeOfficers[$district]) && count($policeOfficers[$district]) > 0) {
                    $assignedOfficer = $faker->randomElement($policeOfficers[$district]);
                    ComplaintAssignment::create([
                        'complaintID' => $complaint->complaintID,
                        'userID_police' => $assignedOfficer->userID,
                        'assigned_by_admin' => 1,
                        'assignment_type' => 'admin_assign',
                        'assignment_reason' => 'District jurisdiction match.',
                        'assigned_at' => now(),
                        'assignment_status' => 'active'
                    ]);
                } else {
                    $complaint->update(['status' => 'Submitted', 'police_note' => null]);
                }
            }

            Evidence::create([
                'complaintID' => $complaint->complaintID,
                'file_name' => 'incident_photo.png',
                'file_path' => 'evidence/' . $dummyImageName,
                'file_type' => 'image/png',
                'uploaded_time' => now()
            ]);

            if ($faker->boolean(50)) {
                Evidence::create([
                    'complaintID' => $complaint->complaintID,
                    'file_name' => 'witness_statement.pdf',
                    'file_path' => 'evidence/' . $dummyDocName,
                    'file_type' => 'application/pdf',
                    'uploaded_time' => now()
                ]);
            }
        }
    }
}
