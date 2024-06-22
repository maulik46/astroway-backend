<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminModel\SystemFlag;

class AddAstroMallMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemFlag::create([
            'valueType' => 'Text',
            'name' => 'vedicAstroAPI',
            'value' => '436d6872-b655-5259-aa0b-133e20301cf3',
            'isActive' => 1,
            'isDelete' => 0,
            'displayName' => 'Vedic Astrology Api',
            'flagGroupId' => 9,
            'description' => ''
        ]);
    }
}
