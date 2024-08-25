<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'task_files';

    protected $fillable = [
        'name',
        'path',
        'task_id',
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
