<?php

namespace App\Services\Profession;

use App\Models\CrmCampaign;
use App\Models\FunnelStep;
use App\Models\Lead;
use Carbon\Carbon;
use Exception;

class RoutineService
{
    public function sendCrmMessage($request)
    {
        try{
            $crmCampaigns = CrmCampaign::where('is_active', true)
                ->whereDate('start_date', Carbon::now())
                ->whereRaw('HOUR(start_time) = ? AND MINUTE(start_time) = ?', [
                    Carbon::now()->hour,
                    Carbon::now()->minute,
                ])
                ->get();

            foreach($crmCampaigns as $crmCampaign){
                $channels = explode(',', $crmCampaign->channels);
            
                if(isset($crmCampaign->funnel_step_id)){
                    $step_id = $crmCampaign->funnel_step_id;
                    $funnelStep = FunnelStep::find($step_id);

                    Lead::whereHas('steps', function($query) use ($step_id){
                        $query->where('step_id', $step_id);
                    });
                }


                if(in_array('Email', $channels)){
                    // $crmCampaign->

                }

                if(in_array('Whatsapp', $channels)){
                    
                }
                
            }
      
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
