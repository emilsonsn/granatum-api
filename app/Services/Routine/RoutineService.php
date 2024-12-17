<?php

namespace App\Services\Profession;

use App\Models\CrmCampaign;
use Exception;

class RoutineService
{
    public function sendCrmMessage($request)
    {
        try{
            CrmCampaign::where('is_active', true)
                ->where('type', );
      
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
