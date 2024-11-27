<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrCampaign extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'hr_campaigns';

    public $fillable = [
        'title',
        'message',
        'type',
        'recurrence_type',
        'selection_process_id',
        'status_id',
        'channels',
        'start_date',
    ];

    public function selectionProcess(){
        return $this->belongsTo(SelectionProcess::class);
    }

    public function status()  {
        return $this->belongsTo(Status::class);
    }
}
