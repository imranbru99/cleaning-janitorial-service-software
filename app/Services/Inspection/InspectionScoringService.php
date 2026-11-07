<?php

namespace App\Services\Inspection;

use App\Enums\InspectionResult;
use App\Enums\JobStatus;
use App\Models\CleaningJob;
use App\Models\CorrectiveAction;
use App\Models\Inspection;
use App\Models\InspectionAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InspectionScoringService
{
    public function recordAnswers(Inspection $inspection, array $answers): Inspection
    {
        return DB::transaction(function () use ($inspection, $answers) {
            $weightedScore = 0.0;
            $weightedMax = 0.0;

            foreach ($answers as $answer) {
                $questionId = $answer['inspection_question_id'];
                $score = (float) ($answer['score'] ?? 0);
                $max = (float) ($answer['max_score'] ?? 10);
                $weight = (float) ($answer['weight'] ?? 1);

                InspectionAnswer::query()->updateOrCreate(
                    [
                        'inspection_id' => $inspection->id,
                        'inspection_question_id' => $questionId,
                    ],
                    [
                        'tenant_id' => $inspection->tenant_id,
                        'score' => $score,
                        'passed' => $answer['passed'] ?? ($score >= ($max * 0.7)),
                        'notes' => $answer['notes'] ?? null,
                    ]
                );

                $weightedScore += $score * $weight;
                $weightedMax += $max * $weight;
            }

            $percentage = $weightedMax > 0 ? round(($weightedScore / $weightedMax) * 100, 2) : 0;
            $thresholds = $inspection->template?->scoring_rules
                ?? config('cleaning.inspection_thresholds', []);
            $result = InspectionResult::fromScore($percentage, $thresholds);

            $inspection->update([
                'score' => $percentage,
                'result' => $result,
                'status' => 'completed',
                'completed_at' => now(),
                'summary' => $inspection->summary,
            ]);

            if ($result === InspectionResult::Failed) {
                $this->handleFailure($inspection);
            } elseif ($inspection->cleaning_job_id) {
                CleaningJob::query()->whereKey($inspection->cleaning_job_id)->update([
                    'status' => JobStatus::Approved,
                ]);
            }

            return $inspection->fresh(['answers', 'correctiveActions']);
        });
    }

    public function handleFailure(Inspection $inspection): CorrectiveAction
    {
        $action = CorrectiveAction::query()->create([
            'tenant_id' => $inspection->tenant_id,
            'inspection_id' => $inspection->id,
            'cleaning_job_id' => $inspection->cleaning_job_id,
            'title' => 'Corrective action for inspection #'.$inspection->id,
            'description' => 'Inspection failed with score '.$inspection->score.'. Rework and reinspection required.',
            'status' => 'open',
            'due_at' => now()->addDays(2),
        ]);

        if ($inspection->cleaning_job_id) {
            CleaningJob::query()->whereKey($inspection->cleaning_job_id)->update([
                'status' => JobStatus::ReworkRequired,
            ]);
        }

        return $action;
    }
}
