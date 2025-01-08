<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutomationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;


    /**
     * Create a new message instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    
    public function build()
    {
        return $this->view('emails.automation')
                    ->with([
                        'message' => $this->message,
                    ])
                    ->subject('Andrade Engenharia - Notificação');
    }

}
