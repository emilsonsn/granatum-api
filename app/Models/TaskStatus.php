<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'task_status';

    protected $fillable = [
        'name',
        'color',
    ];

    public function status(){
        return $this->hasMany(Task::class);
    }
}
