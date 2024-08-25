<?php

namespace Database\Seeders;

use App\Models\CompanyPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyPositionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
           ['position' => 'Admin'],
           ['position' => 'Financial'],
           ['position' => 'Supplies'],
           ['position' => 'Requester']
        ];

        foreach($positions as $position){
            
            CompanyPosition::firstOrCreate($position, $position);
        }
    }
}
