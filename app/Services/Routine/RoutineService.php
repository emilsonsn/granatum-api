<?php

namespace App\Services\Routine;

use App\Mail\AutomationMail;
use App\Models\Candidate;
use App\Models\CrmCampaign;
use App\Models\HrCampaign;
use App\Models\Lead;
use App\Traits\EvolutionTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class RoutineService
{
    use EvolutionTrait;

    public function sendCrmMessage()
    {

        try {
            $today = Carbon::now();
    
            $crmCampaigns = CrmCampaign::where('is_active', true)
                ->whereDate('start_date', $today)
                ->whereRaw('HOUR(start_time) = ? AND MINUTE(start_time) = ?', [
                    Carbon::now()->hour,
                    Carbon::now()->minute,
                ])                
                ->get();
    
            foreach ($crmCampaigns as $crmCampaign) {
                $channels = explode(',', $crmCampaign->channels);
    
                if (isset($crmCampaign->funnel_step_id)) {
                    $leads = Lead::whereHas('steps', function ($query) use ($crmCampaign) {
                        $query->where('step_id', $crmCampaign->funnel_step_id);
                    })->get();
                } else {
                    $leads = Lead::where('funnel_id', $crmCampaign->funnel_id)->get();
                }
    
                if (in_array('Email', $channels)) {
                    foreach ($leads as $lead) {
                        Mail::to($lead->email)
                            ->send(new AutomationMail($crmCampaign->message));                    }
                }
    
                if (in_array('Whatsapp', $channels)) {
                    foreach ($leads as $lead) {
                        $number = $this->traitNumber($lead->phone);
                        $this->sendMessage(
                            $number,
                            $crmCampaign->message
                        );                    }
                }
            }
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    

    public function sendHrMessage()
    {
        try {
            $today = Carbon::now();
    
            $hrCampaigns = HrCampaign::where('is_active', true)
                ->whereDate('start_date', $today)
                ->whereRaw('HOUR(start_time) = ? AND MINUTE(start_time) = ?', [
                    Carbon::now()->hour,
                    Carbon::now()->minute,
                ])
                ->get();
    
            foreach ($hrCampaigns as $hrCampaign) {
                $channels = explode(',', $hrCampaign->channels);
    
                if (isset($hrCampaign->status_id)) {
                    $candidates = Candidate::whereHas('candidateStatuses', function ($query) use ($hrCampaign) {
                        $query->where('status_id', $hrCampaign->status_id);
                    })->get();
                } else {
                    $candidates = Candidate::whereHas('candidateStatuses.status', function ($query) use ($hrCampaign) {
                        $query->where('selection_process_id', $hrCampaign->selection_process_id);
                    })->get();
                }
    
                if (in_array('Email', $channels)) {
                    foreach ($candidates as $candidate) {
                        Mail::to($candidate->email)
                            ->send(new AutomationMail($hrCampaign->message));
                    }
                }
    
                if (in_array('Whatsapp', $channels)) {
                    foreach ($candidates as $candidate) {
                        $number = $this->traitNumber($candidate->number);
                        $this->sendMessage(
                            $number,
                            $hrCampaign->message
                        );
                    }
                }
            }
    
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function traitNumber($number): string {
        $number = preg_replace('/\D/', '', $number);
    
        if (substr($number, 0, 2) !== '55') {
            $number = '55' . $number;
        }
    
        return '+' . $number;
    }
    
    
}
