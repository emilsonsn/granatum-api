<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'candidates';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'cpf',
        'phone',
        'is_active',
        'profession_id',
    ];

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function candidateStatuses()
    {
        return $this->hasMany(CandidateStatus::class);
    }
}