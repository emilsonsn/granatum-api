<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCampaign extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'crm_campaigns';

    public $fillable = [
        'title',
        'message',
        'type',
        'recurrence_type',
        'funnel_id',
        'funnel_step_id',
        'channels',
        'start_date',
    ];

    public function funnel(){
        return $this->belongsTo(Funnel::class);
    }

    public function funnelStep()  {
        return $this->belongsTo(FunnelStep::class);
    }
}
