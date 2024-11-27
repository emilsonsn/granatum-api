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
        'cep',
        'state',
        'city',
        'neighborhood',
        'street',
        'number',
        'marital_status',
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

    public function getSelectionProcesses()
    {
        return SelectionProcess::query()
            ->join('status', 'status.selection_process_id', '=', 'selection_process.id')
            ->join('candidate_status', 'candidate_status.status_id', '=', 'status.id')
            ->where('candidate_status.candidate_id', $this->id)
            ->select('selection_process.*')
            ->distinct()
            ->get();
    }
    

    public function files()
    {
        return $this->hasMany(CandidateAttachment::class);
    }

}