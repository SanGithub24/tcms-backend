<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ComplaintCategory;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Harassment or Intimidation',
                'description' => 'Unwanted contact or behavior that makes someone feel unsafe.',
                'icon' => 'AlertTriangle',
                'status' => 'Active',
            ],
            [
                'name' => 'Overcharging or Scams',
                'description' => 'Being charged excessive prices or becoming a victim of fraud.',
                'icon' => 'DollarSign',
                'status' => 'Active',
            ],
            [
                'name' => 'Food Quality Issue',
                'description' => 'Problems related to food hygiene, quality, or safety.',
                'icon' => 'Utensils',
                'status' => 'Active',
            ],
            [
                'name' => 'Lost or Stolen Items',
                'description' => 'Report lost belongings or incidents of theft.',
                'icon' => 'Package',
                'status' => 'Active',
            ],
            [
                'name' => 'Other Issue',
                'description' => 'Any other complaints not covered by the standard categories.',
                'icon' => 'MoreHorizontal',
                'status' => 'Active',
            ],
        ];

        foreach ($categories as $category) {
            ComplaintCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
