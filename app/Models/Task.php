<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'tasks';

    protected $fillable = [
        'name',
        'user_id',
        'concluded_at',
        'description ',
        'task_status_id'
    ];

    public function status(){
        return $this->belongsTo(TaskStatus::class);
    }
   
}
