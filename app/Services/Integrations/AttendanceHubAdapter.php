<?php

namespace App\Services\Integrations;

use App\Enums\AttendanceMethod;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class AttendanceHubAdapter
{
    public function recordManual(Employee $employee, array $data = []): AttendanceLog
    {
        $checkedIn = isset($data['checked_in_at']) ? Carbon::parse($data['checked_in_at']) : now();
        $checkedOut = isset($data['checked_out_at']) ? Carbon::parse($data['checked_out_at']) : null;
        $break = (int) ($data['break_minutes'] ?? 0);
        $worked = null;

        if ($checkedOut) {
            $worked = round(max(0, $checkedIn->diffInMinutes($checkedOut) - $break) / 60, 2);
        }

        return AttendanceLog::query()->create([
            'tenant_id' => $employee->tenant_id ?? Tenancy::id(),
            'employee_id' => $employee->id,
            'branch_id' => $employee->branch_id,
            'work_date' => $data['work_date'] ?? $checkedIn->toDateString(),
            'checked_in_at' => $checkedIn,
            'checked_out_at' => $checkedOut,
            'break_minutes' => $break,
            'worked_hours' => $worked,
            'overtime_hours' => $data['overtime_hours'] ?? 0,
            'method' => $data['method'] ?? AttendanceMethod::Manual->value,
            'status' => $data['status'] ?? 'present',
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'device_id' => $data['device_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function syncDevices(): array
    {
        if (! class_exists(\ImranDevBd\AttendanceHub\Facades\AttendanceHub::class)) {
            return [
                'success' => false,
                'message' => 'Attendance Hub package is not available.',
                'synced' => 0,
            ];
        }

        try {
            // Device protocols vary by vendor; sync through the package facade when configured.
            $result = \ImranDevBd\AttendanceHub\Facades\AttendanceHub::sync();

            return [
                'success' => true,
                'message' => 'Device sync requested.',
                'result' => $result,
                'synced' => is_countable($result) ? count($result) : 0,
            ];
        } catch (Throwable $e) {
            Log::warning('Attendance device sync failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'synced' => 0,
            ];
        }
    }
}
