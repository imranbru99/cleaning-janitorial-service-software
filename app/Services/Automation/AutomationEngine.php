<?php

namespace App\Services\Automation;

use App\Models\AutomationExecution;
use App\Models\AutomationRule;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Log;
use Throwable;

class AutomationEngine
{
    public function dispatch(string $trigger, array $context = [], ?int $tenantId = null): void
    {
        $tenantId ??= Tenancy::id() ?? ($context['tenant_id'] ?? null);
        if (! $tenantId) {
            return;
        }

        $rules = AutomationRule::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('trigger', $trigger)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            if (! $this->matchesConditions($rule->conditions ?? [], $context)) {
                continue;
            }

            $execution = AutomationExecution::withoutGlobalScopes()->create([
                'tenant_id' => $tenantId,
                'automation_rule_id' => $rule->id,
                'status' => 'pending',
                'context' => $context,
            ]);

            try {
                $this->runActions($rule->actions ?? [], $context);
                $execution->update([
                    'status' => 'completed',
                    'executed_at' => now(),
                ]);
            } catch (Throwable $e) {
                Log::error('Automation failed', [
                    'rule' => $rule->id,
                    'error' => $e->getMessage(),
                ]);
                $execution->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'executed_at' => now(),
                ]);
            }
        }
    }

    protected function matchesConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $key => $expected) {
            if (($context[$key] ?? null) != $expected) {
                return false;
            }
        }

        return true;
    }

    protected function runActions(array $actions, array $context): void
    {
        foreach ($actions as $action) {
            $type = $action['type'] ?? null;

            match ($type) {
                'log' => Log::info('Automation action', ['message' => $action['message'] ?? '', 'context' => $context]),
                'notify' => Log::info('Automation notify', ['channel' => $action['channel'] ?? 'mail', 'context' => $context]),
                default => Log::info('Unhandled automation action', ['action' => $action]),
            };
        }
    }
}
