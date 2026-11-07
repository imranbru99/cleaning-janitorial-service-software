<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\CleaningJob;
use App\Models\JobTimeLog;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        return CleaningJob::query()->with(['customer', 'assignments.employee'])->latest()->paginate(20);
    }

    public function show(CleaningJob $job)
    {
        return $job->load(['customer', 'assignments.employee', 'tasks', 'timeLogs']);
    }

    public function update(Request $request, CleaningJob $job)
    {
        $data = $request->validate([
            'status' => 'sometimes|string',
            'completion_notes' => 'nullable|string',
            'checklist_progress' => 'nullable|array',
        ]);

        if (isset($data['status'])) {
            $status = JobStatus::from($data['status']);
            if ($status === JobStatus::InProgress && ! $job->started_at) {
                $data['started_at'] = now();
            }
            if ($status === JobStatus::Completed) {
                $data['completed_at'] = now();
            }

            JobTimeLog::query()->create([
                'tenant_id' => $job->tenant_id,
                'cleaning_job_id' => $job->id,
                'employee_id' => auth()->user()?->employee?->id,
                'action' => $data['status'],
                'logged_at' => now(),
            ]);
        }

        $job->update($data);

        return $job->fresh();
    }
}
