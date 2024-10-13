<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        PropertyType::query()->insert([
            [
                'id' => 1,
                'name_en' => 'House',
                'name_ar' => 'منزل',
            ],
            [
                'id' => 2,
                'name_en' => 'Farm',
                'name_ar' => 'مزرعة',
            ],
            [
                'id' => 3,
                'name_en' => 'Market',
                'name_ar' => 'محل تجاري',
            ],
        ]);
    }
}
