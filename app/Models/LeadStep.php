<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStep extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'lead_step';

    protected $fillable = [
        'position',
        'lead_id',
        'step_id',
    ];

    public function lead(){
        return $this->belongsTo(Lead::class);
    }

    public function step(){
        return $this->belongsTo(FunnelStep::class);
    }

}
