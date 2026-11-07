<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Enums\JobStatus;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CleaningJob;
use App\Models\JobAssignment;
use App\Models\Schedule;
use App\Models\ScheduleAssignment;
use App\Support\Tenancy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class BookingService
{
    public function create(array $data, array $items = []): Booking
    {
        return DB::transaction(function () use ($data, $items) {
            $subtotal = collect($items)->sum(fn ($item) => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0));
            $tax = collect($items)->sum(fn ($item) => (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)) * (($item['tax_rate'] ?? 0) / 100));
            $discount = (float) ($data['discount_total'] ?? 0);
            $total = max(0, $subtotal + $tax - $discount);

            $booking = Booking::query()->create([
                'tenant_id' => $data['tenant_id'] ?? Tenancy::id(),
                'branch_id' => $data['branch_id'] ?? null,
                'customer_id' => $data['customer_id'],
                'customer_address_id' => $data['customer_address_id'] ?? null,
                'reference' => $data['reference'] ?? 'BK-'.strtoupper(Str::random(8)),
                'status' => $data['status'] ?? BookingStatus::Pending->value,
                'type' => $data['type'] ?? 'one_time',
                'scheduled_start' => $data['scheduled_start'] ?? null,
                'scheduled_end' => $data['scheduled_end'] ?? null,
                'estimated_duration_minutes' => $data['estimated_duration_minutes'] ?? null,
                'subtotal' => $subtotal,
                'tax_total' => $tax,
                'discount_total' => $discount,
                'total' => $total,
                'deposit_amount' => $data['deposit_amount'] ?? 0,
                'special_instructions' => $data['special_instructions'] ?? null,
                'preferences' => $data['preferences'] ?? [],
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            foreach ($items as $item) {
                BookingItem::query()->create([
                    'tenant_id' => $booking->tenant_id,
                    'booking_id' => $booking->id,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'line_total' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                    'meta' => $item['meta'] ?? null,
                ]);
            }

            return $booking->load('items');
        });
    }

    public function transition(Booking $booking, BookingStatus $status): Booking
    {
        $booking->update(['status' => $status]);

        return $booking->fresh();
    }

    public function assignEmployees(Booking $booking, array $employeeIds, ?string $role = 'cleaner'): CleaningJob
    {
        if (! $booking->scheduled_start || ! $booking->scheduled_end) {
            throw new InvalidArgumentException('Booking must have scheduled start and end times.');
        }

        $this->assertNoConflicts($employeeIds, $booking->scheduled_start, $booking->scheduled_end, $booking->id);

        return DB::transaction(function () use ($booking, $employeeIds, $role) {
            $job = CleaningJob::query()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'tenant_id' => $booking->tenant_id,
                    'branch_id' => $booking->branch_id,
                    'customer_id' => $booking->customer_id,
                    'customer_address_id' => $booking->customer_address_id,
                    'reference' => 'JOB-'.$booking->reference,
                    'title' => 'Cleaning for booking '.$booking->reference,
                    'status' => JobStatus::Assigned,
                    'scheduled_start' => $booking->scheduled_start,
                    'scheduled_end' => $booking->scheduled_end,
                    'instructions' => $booking->special_instructions,
                    'checklist' => $booking->items->first()?->service?->checklist ?? [],
                ]
            );

            foreach ($employeeIds as $employeeId) {
                JobAssignment::query()->updateOrCreate(
                    ['cleaning_job_id' => $job->id, 'employee_id' => $employeeId],
                    [
                        'tenant_id' => $booking->tenant_id,
                        'role' => $role,
                        'assigned_at' => now(),
                    ]
                );
            }

            $schedule = Schedule::query()->create([
                'tenant_id' => $booking->tenant_id,
                'branch_id' => $booking->branch_id,
                'booking_id' => $booking->id,
                'schedule_date' => $booking->scheduled_start->toDateString(),
                'starts_at' => $booking->scheduled_start,
                'ends_at' => $booking->scheduled_end,
                'status' => 'published',
            ]);

            foreach ($employeeIds as $employeeId) {
                ScheduleAssignment::query()->create([
                    'tenant_id' => $booking->tenant_id,
                    'schedule_id' => $schedule->id,
                    'employee_id' => $employeeId,
                    'role' => $role,
                ]);
            }

            $booking->update(['status' => BookingStatus::Assigned]);

            return $job->load('assignments');
        });
    }

    public function assertNoConflicts(array $employeeIds, $startsAt, $endsAt, ?int $ignoreBookingId = null): void
    {
        foreach ($employeeIds as $employeeId) {
            $conflict = ScheduleAssignment::query()
                ->where('employee_id', $employeeId)
                ->whereHas('schedule', function ($query) use ($startsAt, $endsAt, $ignoreBookingId) {
                    $query->where('starts_at', '<', $endsAt)
                        ->where('ends_at', '>', $startsAt)
                        ->when($ignoreBookingId, fn ($q) => $q->where('booking_id', '!=', $ignoreBookingId));
                })
                ->exists();

            if ($conflict) {
                throw new RuntimeException("Employee #{$employeeId} has a scheduling conflict.");
            }
        }
    }
}
