<?php

use App\Enums\BookingStatus;
use App\Enums\InspectionResult;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Inspection;
use App\Models\InspectionQuestion;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\InvoiceService;
use App\Services\Booking\BookingService;
use App\Services\Inspection\InspectionScoringService;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\DemoSeeder::class);
    $this->tenant = Tenant::query()->where('slug', 'northline')->firstOrFail();
    Tenancy::set($this->tenant);
});

it('creates a booking, assigns staff, invoices, and scores inspections', function () {
    $customer = Customer::query()->firstOrFail();
    $service = Service::query()->firstOrFail();
    $employee = Employee::query()->firstOrFail();

    $bookingService = app(BookingService::class);
    $booking = $bookingService->create([
        'customer_id' => $customer->id,
        'customer_address_id' => $customer->addresses()->first()?->id,
        'scheduled_start' => now()->addDays(3)->setTime(10, 0),
        'scheduled_end' => now()->addDays(3)->setTime(12, 0),
        'status' => BookingStatus::Confirmed->value,
    ], [[
        'service_id' => $service->id,
        'description' => $service->name,
        'quantity' => 1,
        'unit_price' => $service->base_price,
    ]]);

    expect($booking->total)->toBeGreaterThan(0);

    $job = $bookingService->assignEmployees($booking->fresh('items'), [$employee->id]);
    expect($job->assignments)->toHaveCount(1);

    $invoice = app(InvoiceService::class)->createFromBooking($booking->fresh('items'));
    expect($invoice->number)->toStartWith('INV-');

    $payment = app(InvoiceService::class)->recordPayment($invoice, [
        'amount' => $invoice->total,
        'method' => 'manual',
        'verified' => true,
    ]);
    expect($payment->status->value)->toBe('completed');
    expect($invoice->fresh()->status->value)->toBe('paid');

    $question = InspectionQuestion::query()->firstOrFail();
    $inspection = Inspection::query()->create([
        'tenant_id' => $this->tenant->id,
        'cleaning_job_id' => $job->id,
        'customer_id' => $customer->id,
        'status' => 'in_progress',
        'result' => InspectionResult::Pending,
    ]);

    $scored = app(InspectionScoringService::class)->recordAnswers($inspection, [[
        'inspection_question_id' => $question->id,
        'score' => 3,
        'max_score' => 10,
        'weight' => 1,
    ]]);

    expect($scored->result)->toBe(InspectionResult::Failed)
        ->and($scored->correctiveActions)->not->toBeEmpty();
});

it('allows demo owner to authenticate to the admin panel login page', function () {
    $this->get('/admin/login')->assertOk();

    $owner = User::query()->where('email', 'owner@cleaningdemo.test')->firstOrFail();
    $this->actingAs($owner)->get('/admin')->assertOk();
});

it('serves the public marketing homepage', function () {
    $this->get('/')->assertOk()->assertSee('CleanOps');
});
