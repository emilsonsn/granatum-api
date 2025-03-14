<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'vacancies';

    protected $fillable = [
        'title',
        'description',
        'profession_id',
    ];

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function selectionProcesses()
    {
        return $this->hasMany(SelectionProcess::class);
    }
}