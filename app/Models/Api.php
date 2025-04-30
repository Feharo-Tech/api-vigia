<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Services\ApiNotificationService;


class Api extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'url', 'method', 'expected_response',
        'expected_status_code', 'check_interval', 'is_active', 'headers',
        'body', 'last_checked_at', 'error_threshold', 'timeout_threshold', 'should_notify', 'content_type'
    ];

    protected $casts = [
        'headers' => 'array',
        'body' => 'array',
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    public const CHECK_INTERVALS = [
        1 => '1 minuto',
        5 => '5 minutos',
        10 => '10 minutos',
        15 => '15 minutos',
        30 => '30 minutos',
        60 => '1 hora',
    ];

    public const CONTENT_TYPE = [
        'application/json' => 'JSON',
        'application/x-www-form-urlencoded' => 'Formulário',
        'text/plain' => 'Texto simples',
        'multipart/form-data' => 'Multipart',
        'application/xml' => 'XML',
        'application/octet-stream' => 'Binário',
    ];

    public static function createFromRequest(array $data)
    {
        $data['user_id'] = auth()->id();
        $data['is_active'] = $data['is_active'] ?? false;
        $data['headers'] = isset($data['headers']) ? json_decode($data['headers'], true) : null;
        $data['body'] = isset($data['body']) ? json_decode($data['body'], true) : null;
        return self::create($data);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusChecks()
    {
        return $this->hasMany(ApiStatusCheck::class);
    }

    public function latestStatusCheck()
    {
        return $this->hasOne(ApiStatusCheck::class)->latestOfMany();
    }

    public function getHeadersAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }
        return $value;
    }

    public function getBodyAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }
        return $value;
    }

    public function setHeadersAttribute($value)
    {
        $this->attributes['headers'] = is_string($value) ? $value : json_encode($value);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = is_string($value) ? $value : json_encode($value);
    }

    public function updateFromRequest(array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        $data['headers'] = isset($data['headers']) ? json_decode($data['headers'], true) : null;
        $data['body'] = isset($data['body']) ? json_decode($data['body'], true) : null;
        $data['should_notify'] = $data['should_notify'] ?? false;
        $this->update($data);
    }

    public function formatJsonFields()
    {
        if (is_array($this->headers)) {
            $this->headers = json_encode($this->headers, JSON_PRETTY_PRINT);
        }
        if (is_array($this->body)) {
            $this->body = json_encode($this->body, JSON_PRETTY_PRINT);
        }
    }

    public function uptimeStats()
    {
        $total = $this->statusChecks()->count();
        $success = $this->statusChecks()->where('success', true)->count();
        $failure = $total - $success;

        $uptime = $total > 0 ? round(($success / $total) * 100, 2) : 0;
        $avgResponse = $this->statusChecks()->where('success', true)->avg('response_time') * 1000;

        $performanceStatus = 'unknown'; 

        if ($uptime >= 99 && $avgResponse <= 200) {
            $performanceStatus = 'excellent';
        } elseif ($uptime >= 95 && $avgResponse <= 500) {
            $performanceStatus = 'good';
        } elseif ($uptime >= 90 && $avgResponse <= 1000) {
            $performanceStatus = 'fair';
        } else {
            $performanceStatus = 'poor';
        }

        $lastResponseTime = optional($this->statusChecks()->latest()->first())->response_time * 1000;
        $lastResponseTime = $lastResponseTime !== null ? round($lastResponseTime, 3) : null;

        return [
            'uptime' => $uptime,
            'average_response_time' => $avgResponse ? round($avgResponse, 2) : null,
            'last_response_time' => $lastResponseTime,
            'success_checks' => $success,
            'failure_checks' => $failure,
            'total_checks' => $total,
            'performance_status' => $performanceStatus,
        ];
    }



    public function performStatusCheck()
    {
        $start = microtime(true);

        $response = Http::withHeaders(json_decode($this->headers, true)  ?? [])
            ->timeout(10)
            ->{$this->method}($this->url, json_decode($this->body, true)?? []);

        $time = microtime(true) - $start;
        $success = $response->status() === $this->expected_status_code;

        if ($this->expected_response) {
            $success = $success && str_contains($response->body(), $this->expected_response);
        }

        $statusCheck = $this->statusChecks()->create([
            'status_code' => $response->status(),
            'response_time' => $time,
            'success' => $success,
            'response_body' => $success ? null : substr($response->body(), 0, 1000),
            'error_message' => $success ? null : 'Incompatibilidade no código de status ou no corpo da resposta.',
        ]);

        $this->evaluateAndNotify($statusCheck, 0);

        return [
            'success' => $success,
            'message' => 'Verificação realizada: ' . ($success ? 'Online' : 'Offline'),
        ];
    }

    public function statusHistory()
    {
        return $this->statusChecks()
            ->selectRaw('DATE(created_at) as date, HOUR(created_at) as hour, AVG(response_time) as avg_response_time, SUM(CASE WHEN success THEN 1 ELSE 0 END) as success_count, COUNT(*) as total_checks')
            ->groupBy('date', 'hour')
            ->orderByDesc('date')
            ->orderByDesc('hour')
            ->get();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'api_tag', 'api_id', 'tag_id');
    }

    public function evaluateAndNotify(ApiStatusCheck $statusCheck, float $responseTime)
    {
        if (!$this->should_notify) {
            return;
        }

        $errorCount = $this->statusChecks()
            ->where('success', false)
            ->orderByDesc('created_at')
            ->take($this->error_threshold)
            ->count();

        if ($errorCount >= $this->error_threshold) {
            ApiNotificationService::notify(
                $this,
                'Muitos erros consecutivos',
                $errorCount,
                $responseTime,
                $statusCheck->status_code
            );
        } elseif ($responseTime > $this->timeout_threshold) {
            ApiNotificationService::notify(
                $this,
                'Tempo de resposta excedido',
                $errorCount,
                $responseTime,
                $statusCheck->status_code
            );
        }
    }

    public function scopeVisibleToUser($query, $user)
    {
        if ($user->is_admin) {
            return $query;
        }

        return $query->where('is_active', true);
    }
}