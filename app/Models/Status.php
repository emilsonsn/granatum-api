<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'status';

    protected $fillable = [
        'title',
        'color',
        'selection_process_id',
    ];

    public function selectionProcess()
    {
        return $this->belongsTo(SelectionProcess::class);
    }

    public function candidateStatuses()
    {
        return $this->hasMany(CandidateStatus::class)->with('candidate');
    }
}