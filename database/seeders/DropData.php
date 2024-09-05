<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DropData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach($users as $user){
            $user->orders()->delete();
            $user->releases()->delete();
            $user->solicitations()->delete();
            $user->tasks()->delete();
            $user->delete();
        }
    }
}
