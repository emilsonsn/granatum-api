<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateAttachment extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'candidate_attachments';

    protected $fillable = [
        'name',
        'path',
        'selection_process_id',
        'candidate_id'
    ];

    public function getPathAttribute($path){
        return isset($path) ? asset('storage/' . $path) : null;
    }

    public function candidate(){
        return $this->belongsTo(candidate::class);
    }

    public function selectionProcess(){
        return $this->belongsTo(SelectionProcess::class);
    }
}