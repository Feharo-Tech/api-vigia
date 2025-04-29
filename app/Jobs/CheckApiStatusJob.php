<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Api;
use App\Models\ApiStatusCheck;
use App\Models\NotificationSetting;
use App\Notifications\ApiDownNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;


class CheckApiStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle()
    {
        try {
            $startTime = microtime(true);
            
            $response = Http::withHeaders(json_decode($this->api->headers, true) ?? [])
                ->timeout(10)
                ->{$this->api->method}($this->api->url, json_decode($this->api->body, true) ?? []);
            
            $responseTime = microtime(true) - $startTime;
            
            $success = $response->status() === $this->api->expected_status_code;
            
            if ($this->api->expected_response) {
                $success = $success && str_contains($response->body(), $this->api->expected_response);
            }

            $statusCheck = $this->api->statusChecks()->create([
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'success' => $success,
                'response_body' => $success ? null : substr($response->body(), 0, 1000),
                'error_message' => $success ? null : 'Incompatibilidade no código de status ou no corpo da resposta.',
            ]);

            $this->api->evaluateAndNotify($statusCheck, $responseTime);

        } catch (\Exception $e) {
            $statusCheck = $this->api->statusChecks()->create([
                'status_code' => 0,
                'response_time' => 0,
                'success' => false,
                'response_body' => null,
                'error_message' => $e->getMessage(),
            ]);
            
            $this->api->evaluateAndNotify($statusCheck, 0);
        }
        finally {
            Api::where('id', $this->api->id)->update(['last_checked_at' => now()]);
        }
    }
}
