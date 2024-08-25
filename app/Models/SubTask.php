<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'sub_tasks';

    protected $fillable = [
        'description',
        'status',
        'task_id'        
    ];

    public function taks(){
        return $this->belongsTo(Task::class);
    }
}
