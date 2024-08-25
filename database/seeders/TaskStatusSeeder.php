<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $statuses = [
            ['name' => 'Pendente', 'color' => '#FF0000'],
            ['name' => 'Em Progresso', 'color' => '#FFFF00'],
            ['name' => 'Concluído', 'color' => '#00FF00'],
            ['name' => 'Cancelado', 'color' => '#FF0000'],
            ['name' => 'Arquivado', 'color' => '#808080'],
        ];
        

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate($status);
        }
    }
}
