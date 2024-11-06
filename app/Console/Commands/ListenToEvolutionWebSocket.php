<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use WebSocket\Client;
use App\Events\EvolutionEvent;
use Illuminate\Support\Facades\Log;

class ListenToEvolutionWebSocket extends Command
{
    protected $signature = 'evolution:listen';
    protected $description = 'Conecta ao WebSocket da Evolution API e retransmite para o frontend';

    public function handle()
    {
        $url = 'wss://api.andradeengenhariaeletrica.com.br'; // WebSocket da Evolution API
        $client = new Client($url);

        while (true) {
            try {
                $message = $client->receive();
                $data = json_decode($message, true);

                Log::info('Mensagem recebida do WebSocket:', $data);
                
                event(new EvolutionEvent($data));
            } catch (\Exception $e) {
                Log::error('Erro na conexão WebSocket: ' . $e->getMessage());
                sleep(5);
            }
        }
    }
}
