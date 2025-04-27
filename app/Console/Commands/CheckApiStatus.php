<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Api;
use App\Jobs\CheckApiStatusJob;

class CheckApiStatus extends Command
{
    protected $signature = 'api:check-status';
    protected $description = 'Verifique o status de todas as APIs ativas';

    public function handle()
    {    
        $apis = Api::where('is_active', true)->get();
        
        if ($apis->isEmpty()) {
            $this->info('Nenhuma API ativa para verificar');
            return;
        }
        
        foreach ($apis as $api) {
            if ($this->shouldCheckApi($api)) {
                CheckApiStatusJob::dispatch($api);
            }
        }
        
        $this->info('Verificação de APIs agendada com sucesso');
    }
    
    protected function shouldCheckApi(Api $api): bool
    {
        if (is_null($api->last_checked_at)) {
            return true;
        }
        
        $nextCheck = $api->last_checked_at->addMinutes($api->check_interval);
        
        return now()->greaterThanOrEqualTo($nextCheck);
    }
}
