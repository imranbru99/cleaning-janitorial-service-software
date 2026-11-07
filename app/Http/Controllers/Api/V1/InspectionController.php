<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Services\Inspection\InspectionScoringService;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index()
    {
        return Inspection::query()->with('cleaningJob')->latest()->paginate(20);
    }

    public function show(Inspection $inspection)
    {
        return $inspection->load(['answers', 'correctiveActions']);
    }

    public function store(Request $request, InspectionScoringService $scoring)
    {
        $data = $request->validate([
            'cleaning_job_id' => 'nullable|exists:cleaning_jobs,id',
            'customer_id' => 'nullable|exists:customers,id',
            'inspection_template_id' => 'nullable|exists:inspection_templates,id',
            'answers' => 'required|array|min:1',
            'answers.*.inspection_question_id' => 'required|exists:inspection_questions,id',
            'answers.*.score' => 'required|numeric',
            'answers.*.max_score' => 'nullable|numeric',
            'answers.*.weight' => 'nullable|numeric',
            'summary' => 'nullable|string',
        ]);

        $inspection = Inspection::query()->create([
            'cleaning_job_id' => $data['cleaning_job_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'inspection_template_id' => $data['inspection_template_id'] ?? null,
            'inspector_id' => auth()->id(),
            'status' => 'in_progress',
            'summary' => $data['summary'] ?? null,
        ]);

        return response()->json($scoring->recordAnswers($inspection, $data['answers']), 201);
    }
}
