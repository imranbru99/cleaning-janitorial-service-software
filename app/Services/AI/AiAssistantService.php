<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiAssistantService
{
    public function isEnabled(): bool
    {
        return (bool) config('ai-hub.enabled', true)
            && filled(config('ai-hub.providers.openai.api_key')
                ?: config('ai-hub.providers.gemini.api_key')
                ?: env('OPENAI_API_KEY')
                ?: env('GEMINI_API_KEY'));
    }

    public function generate(string $feature, string $prompt, array $options = []): array
    {
        if (! $this->isEnabled()) {
            return [
                'success' => false,
                'content' => null,
                'message' => 'AI integrations are optional and not configured.',
            ];
        }

        try {
            if (! class_exists(\ImranDevBd\AiHub\Facades\AIHub::class)) {
                return [
                    'success' => false,
                    'content' => null,
                    'message' => 'AI Hub package is not available.',
                ];
            }

            $response = \ImranDevBd\AiHub\Facades\AIHub::prompt($prompt)
                ->when(! empty($options['model']), fn ($req) => $req->model($options['model']))
                ->generate();

            $content = is_string($response) ? $response : (string) ($response->content ?? $response->text ?? json_encode($response));

            AiUsageLog::query()->create([
                'tenant_id' => Tenancy::id(),
                'user_id' => auth()->id(),
                'feature' => $feature,
                'provider' => $options['provider'] ?? 'default',
                'model' => $options['model'] ?? null,
                'tokens_in' => $options['tokens_in'] ?? 0,
                'tokens_out' => $options['tokens_out'] ?? 0,
                'cost_usd' => $options['cost_usd'] ?? 0,
                'status' => 'success',
            ]);

            return [
                'success' => true,
                'content' => $content,
                'message' => 'Generated successfully. Human review required for consequential actions.',
            ];
        } catch (Throwable $e) {
            Log::warning('AI generation failed', ['feature' => $feature, 'error' => $e->getMessage()]);

            AiUsageLog::query()->create([
                'tenant_id' => Tenancy::id(),
                'user_id' => auth()->id(),
                'feature' => $feature,
                'status' => 'error',
            ]);

            return [
                'success' => false,
                'content' => null,
                'message' => 'AI request failed: '.$e->getMessage(),
            ];
        }
    }

    public function summarizeInspection(string $notes): array
    {
        return $this->generate('inspection_summary', "Summarize this cleaning inspection for a supervisor:\n\n{$notes}");
    }

    public function generateServiceDescription(string $serviceName, string $context = ''): array
    {
        return $this->generate(
            'service_description',
            "Write a professional cleaning service description for: {$serviceName}. Context: {$context}"
        );
    }
}
