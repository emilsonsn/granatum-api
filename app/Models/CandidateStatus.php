<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateStatus extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'candidate_status';

    protected $fillable = [
        'candidate_id',
        'status_id',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class)->with('selectionProcess');
    }
}