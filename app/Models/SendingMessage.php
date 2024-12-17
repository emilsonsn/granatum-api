<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendingMessage extends Model
{
    use HasFactory;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'hr_campaign_id',
        'crm_campaign_id',
    ];

    public function hrCampaign(){
        $this->belongsTo(HrCampaign::class);
    }

    public function crmCampaign(){
        $this->belongsTo(CrmCampaign::class);
    }
}
