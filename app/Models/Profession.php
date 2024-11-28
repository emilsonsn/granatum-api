<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profession extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'professions';

    protected $fillable = [
        'title',
        'description',
    ];

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}