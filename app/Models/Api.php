<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Services\ApiNotificationService;
use Illuminate\Support\Facades\Crypt;


class Api extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'url', 'method', 'expected_response',
        'expected_status_code', 'check_interval', 'is_active', 'headers',
        'body', 'raw_body', 'last_checked_at', 'error_threshold', 'timeout_threshold', 'should_notify', 'content_type', 'certificate_id'
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
        'application/soap+xml' => 'SOAP XML',
        'text/xml; charset=utf-8' => 'Texto XML',
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

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
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
        try {
            $start = microtime(true);

            $contentType = strtolower($this->content_type);
            $headers = json_decode($this->headers, true) ?? [];

            $request = Http::withHeaders($headers)->timeout(10);

            $body = $this->resolveRequestBody();

            $request = $this->applyCertificateIfNeeded($request);
    
            $request = $this->applyContentTypeOptions($request);
    
            $response = $this->sendRequest($request, $body, $contentType);
    
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

        } catch (\Exception $e) {

            $statusCheck = $this->statusChecks()->create([
                'status_code' => 0,
                'response_time' => 0,
                'success' => false,
                'response_body' => null,
                'error_message' => $e->getMessage(),
            ]);
            
            $this->evaluateAndNotify($statusCheck, 0);
        }
        finally {
            Api::where('id', $this->id)->update(['last_checked_at' => now()]);
        }
    }

    private function resolveRequestBody(): string|array
    {
        $contentType = strtolower($this->content_type);

        if (str_contains($contentType, 'xml') || str_contains($contentType, 'soap')) {
            return trim($this->raw_body ?? '');
        }

        return json_decode($this->body, true) ?? [];
    }

    private function applyCertificateIfNeeded($request)
    {
        if (!$this->certificate) {
            return $request;
        }

        $certPath = storage_path("app/private/{$this->certificate->path}");

        if (!file_exists($certPath)) {
            throw new \Exception("Arquivo do certificado não encontrado: {$certPath}");
        }

        $certPassword = $this->certificate->password ? Crypt::decryptString($this->certificate->password) : null;

        return $request->withOptions([
            'cert' => $this->certificate->type === 'pem' ? $certPath : [$certPath, $certPassword]
        ]);
    }

    private function applyContentTypeOptions($request)
    {
        $contentType = strtolower($this->content_type);

        if (str_contains($contentType, 'xml') ||
            str_contains($contentType, 'soap') ||
            str_contains($contentType, 'text/plain') ||
            str_contains($contentType, 'octet-stream')) {
            return $request;
        }

        return match ($contentType) {
            'application/json' => $request->asJson(),
            'application/x-www-form-urlencoded' => $request->asForm(),
            'multipart/form-data' => $request->asMultipart(),
            default => $request,
        };
    }

    private function sendRequest($request, $body, $contentType)
    {
        $method = strtoupper($this->method);

        if ($method === 'OPTIONS') {
            return $request->send('OPTIONS', $this->url);
        }

        if (str_contains($contentType, 'xml')) {
            return $request->withBody(trim($body), $contentType)
                        ->send($method, $this->url);
        }

        return $request->{$this->method}($this->url, $body);
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

    public function getContentTypeLabel(): string
    {
        return self::CONTENT_TYPE[$this->content_type] ?? 'Nenhum';
    }

    public function getCertificateLabel(): string
    {
        return $this->certificate->name ?? 'Nenhum';
    }
}