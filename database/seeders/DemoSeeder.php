<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\InspectionResult;
use App\Enums\InvoiceStatus;
use App\Enums\JobStatus;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Asset;
use App\Models\AttendanceLog;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Branch;
use App\Models\CleaningJob;
use App\Models\Contract;
use App\Models\CorrectiveAction;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Faq;
use App\Models\Inspection;
use App\Models\InspectionAnswer;
use App\Models\InspectionCategory;
use App\Models\InspectionQuestion;
use App\Models\InspectionTemplate;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobAssignment;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Modules\ModuleManager;
use App\Support\Tenancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('cleaning.demo_password', 'Demo@12345');
        $modules = app(ModuleManager::class);
        $modules->syncRegistry();

        $permissions = collect(config('modules.modules'))
            ->flatMap(fn ($m) => $m['permissions'] ?? [])
            ->unique()
            ->values();

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roleMap = [
            'super_admin' => $permissions->all(),
            'platform_admin' => $permissions->all(),
            'business_owner' => $permissions->reject(fn ($p) => str_starts_with($p, 'subscriptions.'))->all(),
            'operations_manager' => ['dashboard.view', 'crm.view', 'crm.manage', 'bookings.view', 'bookings.manage', 'scheduling.view', 'scheduling.manage', 'workforce.view', 'jobs.view', 'jobs.manage', 'quality.view', 'dispatch.manage'],
            'branch_manager' => ['dashboard.view', 'bookings.view', 'bookings.manage', 'workforce.view', 'jobs.view', 'scheduling.view', 'inventory.view'],
            'quality_inspector' => ['dashboard.view', 'quality.view', 'quality.inspect', 'jobs.view'],
            'cleaning_supervisor' => ['dashboard.view', 'jobs.view', 'jobs.execute', 'jobs.manage', 'workforce.view', 'attendance.view'],
            'cleaning_staff' => ['dashboard.view', 'jobs.view', 'jobs.execute', 'attendance.view'],
            'accountant' => ['dashboard.view', 'invoices.view', 'invoices.manage', 'payments.view', 'payments.manage', 'expenses.view', 'reports.view'],
            'customer' => ['portal.access'],
        ];

        foreach ($roleMap as $role => $perms) {
            $roleModel = Role::findOrCreate($role, 'web');
            $roleModel->syncPermissions($perms);
        }

        foreach (['company_admin', 'hr_manager', 'dispatcher', 'scheduler'] as $extra) {
            Role::findOrCreate($extra, 'web');
        }

        $plans = [
            ['name' => 'Starter', 'slug' => 'starter', 'price_monthly' => 49, 'price_yearly' => 490, 'description' => 'CRM, bookings, employees, invoicing', 'modules' => ['crm', 'catalog', 'bookings', 'workforce', 'invoicing', 'payments', 'portal'], 'sort_order' => 1],
            ['name' => 'Professional', 'slug' => 'professional', 'price_monthly' => 129, 'price_yearly' => 1290, 'description' => 'Inspections, contracts, inventory, portal', 'modules' => ['crm', 'catalog', 'bookings', 'scheduling', 'workforce', 'jobs', 'quality', 'portal', 'contracts', 'quotations', 'invoicing', 'payments', 'inventory', 'reports'], 'sort_order' => 2],
            ['name' => 'Business', 'slug' => 'business', 'price_monthly' => 249, 'price_yearly' => 2490, 'description' => 'Branches, attendance, payroll, automation', 'modules' => ['crm', 'catalog', 'bookings', 'scheduling', 'workforce', 'attendance', 'jobs', 'quality', 'portal', 'contracts', 'quotations', 'invoicing', 'payments', 'inventory', 'assets', 'procurement', 'hr', 'payroll', 'expenses', 'branches', 'reports', 'automation', 'tickets'], 'sort_order' => 3],
            ['name' => 'Enterprise', 'slug' => 'enterprise', 'price_monthly' => 499, 'price_yearly' => 4990, 'description' => 'Franchise, API, AI, advanced controls', 'modules' => array_keys(config('modules.modules')), 'sort_order' => 4],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->updateOrCreate(['slug' => $plan['slug']], $plan + [
                'trial_days' => 14,
                'employee_limit' => 100,
                'branch_limit' => 20,
                'is_active' => true,
                'is_public' => true,
            ]);
        }

        $super = User::query()->updateOrCreate(
            ['email' => 'superadmin@cleaningdemo.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'user_type' => 'super_admin',
                'is_platform_user' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $super->assignRole('super_admin');

        $businesses = [
            ['name' => 'Northline Janitorial', 'slug' => 'northline', 'city' => 'Chicago'],
            ['name' => 'BrightHome Cleaning Co', 'slug' => 'brighthome', 'city' => 'Austin'],
        ];

        foreach ($businesses as $index => $biz) {
            $tenant = Tenant::query()->updateOrCreate(
                ['slug' => $biz['slug']],
                [
                    'name' => $biz['name'],
                    'legal_name' => $biz['name'].' LLC',
                    'email' => 'hello@'.$biz['slug'].'.test',
                    'phone' => '+1-555-010'.($index + 1),
                    'currency' => 'USD',
                    'timezone' => 'America/Chicago',
                    'locale' => 'en',
                    'country' => 'US',
                    'status' => 'active',
                    'trial_ends_at' => now()->addDays(10),
                ]
            );

            TenantDomain::query()->updateOrCreate(
                ['domain' => $biz['slug'].'.localhost'],
                ['tenant_id' => $tenant->id, 'is_primary' => true, 'is_verified' => true]
            );

            Subscription::query()->updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'subscription_plan_id' => SubscriptionPlan::query()->where('slug', $index === 0 ? 'enterprise' : 'professional')->value('id'),
                    'status' => SubscriptionStatus::Active,
                    'billing_period' => 'monthly',
                    'starts_at' => now()->subMonth(),
                    'ends_at' => now()->addYear(),
                ]
            );

            Tenancy::set($tenant);
            $modules->activateDefaults($tenant, array_keys(config('modules.modules')));

            $branches = collect([
                ['name' => $biz['city'].' Central', 'code' => 'C01', 'city' => $biz['city']],
                ['name' => $biz['city'].' North', 'code' => 'N01', 'city' => $biz['city']],
            ])->map(fn ($b) => Branch::query()->create($b + [
                'tenant_id' => $tenant->id,
                'email' => strtolower($b['code']).'@'.$biz['slug'].'.test',
                'phone' => '+1-555-02'.random_int(10, 99),
                'country' => 'US',
                'is_active' => true,
            ]));

            if ($index === 0) {
                $this->seedPrimaryTenantUsers($tenant, $branches->first(), $password);
            } else {
                User::query()->updateOrCreate(
                    ['email' => 'owner2@cleaningdemo.test'],
                    [
                        'tenant_id' => $tenant->id,
                        'branch_id' => $branches->first()->id,
                        'name' => 'BrightHome Owner',
                        'password' => Hash::make($password),
                        'user_type' => 'business_owner',
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                )->assignRole('business_owner');
            }

            $category = ServiceCategory::query()->create([
                'tenant_id' => $tenant->id,
                'name' => 'General Cleaning',
                'slug' => 'general-cleaning',
                'description' => 'Everyday and specialty cleaning services',
            ]);

            $serviceNames = [
                'Standard Home Cleaning', 'Deep Cleaning', 'Office Cleaning', 'Commercial Janitorial',
                'Carpet Cleaning', 'Window Cleaning', 'Floor Polishing', 'Bathroom Sanitation',
                'Kitchen Cleaning', 'Upholstery Cleaning', 'Post-Construction Cleaning', 'Move-In Cleaning',
                'Move-Out Cleaning', 'Disinfection Service', 'Night Crew Package', 'Medical Suite Clean',
                'School Classroom Clean', 'Hotel Room Turn', 'Industrial Floor Scrub', 'Glass Tower Exterior',
                'Restroom Restock Route', 'Event Venue Reset',
            ];

            $services = collect($serviceNames)->map(function ($name, $i) use ($tenant, $category) {
                return Service::query()->create([
                    'tenant_id' => $tenant->id,
                    'service_category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.$i,
                    'description' => $name.' performed by trained cleaning professionals.',
                    'pricing_method' => $i % 3 === 0 ? 'hourly' : 'fixed',
                    'base_price' => 80 + ($i * 7),
                    'hourly_rate' => 35 + ($i % 5),
                    'duration_minutes' => 60 + ($i * 15) % 180,
                    'checklist' => ['Dust surfaces', 'Vacuum floors', 'Sanitize bathrooms', 'Empty trash'],
                    'is_active' => true,
                    'is_public' => true,
                ]);
            });

            $customers = collect(range(1, 28))->map(function ($n) use ($tenant, $branches, $biz) {
                $customer = Customer::query()->create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branches[$n % 2]->id,
                    'type' => $n % 4 === 0 ? 'commercial' : 'residential',
                    'name' => "Customer {$biz['slug']} {$n}",
                    'company_name' => $n % 4 === 0 ? "Client Corp {$n}" : null,
                    'email' => "customer{$n}@{$biz['slug']}.test",
                    'phone' => '+1-555-3'.str_pad((string) $n, 3, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'segment' => $n % 2 ? 'premium' : 'standard',
                    'source' => ['web', 'referral', 'sales'][$n % 3],
                ]);

                CustomerAddress::query()->create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer->id,
                    'label' => 'Primary',
                    'address_line1' => ($n * 10).' Market Street',
                    'city' => $biz['city'],
                    'state' => 'IL',
                    'postal_code' => '6060'.$n % 9,
                    'country' => 'US',
                    'square_footage' => 800 + $n * 40,
                    'rooms' => 3 + ($n % 5),
                    'is_primary' => true,
                ]);

                return $customer;
            });

            if ($index === 0) {
                $portalUser = User::query()->where('email', 'customer@cleaningdemo.test')->first();
                if ($portalUser) {
                    $customers->first()->update(['user_id' => $portalUser->id]);
                    $portalUser->update(['tenant_id' => $tenant->id]);
                }
            }

            $employees = collect(range(1, 12))->map(function ($n) use ($tenant, $branches) {
                return Employee::query()->create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branches[$n % 2]->id,
                    'employee_number' => 'E'.str_pad((string) $n, 3, '0', STR_PAD_LEFT),
                    'first_name' => 'Cleaner',
                    'last_name' => (string) $n,
                    'email' => "cleaner{$n}@demo.test",
                    'phone' => '+1-555-4'.str_pad((string) $n, 3, '0', STR_PAD_LEFT),
                    'employment_type' => $n % 3 ? 'full_time' : 'part_time',
                    'status' => 'active',
                    'hire_date' => now()->subMonths($n),
                    'hourly_rate' => 18 + ($n % 6),
                    'skills' => [['residential'], ['commercial'], ['carpet']][$n % 3],
                ]);
            });

            if ($index === 0) {
                $cleanerUser = User::query()->where('email', 'cleaner@cleaningdemo.test')->first();
                if ($cleanerUser) {
                    $employees->first()->update(['user_id' => $cleanerUser->id]);
                }
            }

            foreach ($employees as $employee) {
                AttendanceLog::query()->create([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'branch_id' => $employee->branch_id,
                    'work_date' => now()->toDateString(),
                    'checked_in_at' => now()->setTime(8, 0),
                    'checked_out_at' => now()->setTime(16, 30),
                    'break_minutes' => 30,
                    'worked_hours' => 8,
                    'method' => 'manual',
                    'status' => 'present',
                ]);
            }

            collect(range(1, 8))->each(function ($n) use ($tenant, $customers) {
                Lead::query()->create([
                    'tenant_id' => $tenant->id,
                    'name' => "Lead {$n}",
                    'email' => "lead{$n}@example.test",
                    'phone' => '+1-555-9'.str_pad((string) $n, 3, '0', STR_PAD_LEFT),
                    'status' => ['new', 'contacted', 'qualified', 'proposal'][$n % 4],
                    'source' => 'website',
                    'estimated_value' => 500 * $n,
                    'customer_id' => null,
                ]);
            });

            $template = InspectionTemplate::query()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Standard Quality Checklist',
                'description' => 'Default cleaning quality template',
                'is_active' => true,
                'scoring_rules' => config('cleaning.inspection_thresholds'),
            ]);

            $categories = collect([
                'Floor cleanliness', 'Surface cleanliness', 'Bathroom sanitation', 'Kitchen hygiene',
                'Glass and window quality', 'Waste disposal', 'Odor management', 'Overall presentation',
            ])->map(function ($name, $i) use ($tenant, $template) {
                $cat = InspectionCategory::query()->create([
                    'tenant_id' => $tenant->id,
                    'inspection_template_id' => $template->id,
                    'name' => $name,
                    'weight' => 1,
                    'sort_order' => $i,
                ]);
                InspectionQuestion::query()->create([
                    'tenant_id' => $tenant->id,
                    'inspection_category_id' => $cat->id,
                    'question' => $name.' meets standard?',
                    'type' => 'score',
                    'max_score' => 10,
                    'weight' => 1,
                    'is_required' => true,
                ]);

                return $cat;
            });

            $bookings = collect(range(1, 18))->map(function ($n) use ($tenant, $branches, $customers, $services) {
                $customer = $customers[$n % $customers->count()];
                $service = $services[$n % $services->count()];
                $start = now()->addDays($n - 8)->setTime(9 + ($n % 6), 0);

                $booking = Booking::query()->create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branches[$n % 2]->id,
                    'customer_id' => $customer->id,
                    'customer_address_id' => $customer->addresses()->first()?->id,
                    'reference' => 'BK-'.strtoupper(Str::random(6)),
                    'status' => $n < 10 ? BookingStatus::Completed : BookingStatus::Confirmed,
                    'type' => $n % 3 === 0 ? 'recurring' : 'one_time',
                    'scheduled_start' => $start,
                    'scheduled_end' => (clone $start)->addHours(2),
                    'estimated_duration_minutes' => 120,
                    'subtotal' => $service->base_price,
                    'tax_total' => round($service->base_price * 0.08, 2),
                    'discount_total' => 0,
                    'total' => round($service->base_price * 1.08, 2),
                    'special_instructions' => 'Focus on high-traffic areas.',
                ]);

                BookingItem::query()->create([
                    'tenant_id' => $tenant->id,
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'description' => $service->name,
                    'quantity' => 1,
                    'unit_price' => $service->base_price,
                    'line_total' => $service->base_price,
                ]);

                return $booking;
            });

            foreach ($bookings->take(12) as $i => $booking) {
                $job = CleaningJob::query()->create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $booking->branch_id,
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'customer_address_id' => $booking->customer_address_id,
                    'reference' => 'JOB-'.$booking->reference,
                    'title' => 'Job for '.$booking->reference,
                    'status' => $i < 8 ? JobStatus::Completed : JobStatus::Assigned,
                    'scheduled_start' => $booking->scheduled_start,
                    'scheduled_end' => $booking->scheduled_end,
                    'started_at' => $i < 8 ? $booking->scheduled_start : null,
                    'completed_at' => $i < 8 ? $booking->scheduled_end : null,
                    'checklist' => ['Dust', 'Vacuum', 'Sanitize'],
                    'instructions' => $booking->special_instructions,
                ]);

                JobAssignment::query()->create([
                    'tenant_id' => $tenant->id,
                    'cleaning_job_id' => $job->id,
                    'employee_id' => $employees[$i % $employees->count()]->id,
                    'role' => 'cleaner',
                    'assigned_at' => now()->subDays(2),
                ]);

                if ($i < 8) {
                    $inspection = Inspection::query()->create([
                        'tenant_id' => $tenant->id,
                        'cleaning_job_id' => $job->id,
                        'customer_id' => $job->customer_id,
                        'inspection_template_id' => $template->id,
                        'status' => 'completed',
                        'result' => $i === 0 ? InspectionResult::Failed : InspectionResult::Good,
                        'score' => $i === 0 ? 62 : 86 + ($i % 10),
                        'scheduled_at' => $job->completed_at,
                        'completed_at' => $job->completed_at,
                        'summary' => $i === 0 ? 'Failed bathroom sanitation checks.' : 'Meets quality standards.',
                    ]);

                    foreach ($categories as $cat) {
                        $question = $cat->questions()->first();
                        InspectionAnswer::query()->create([
                            'tenant_id' => $tenant->id,
                            'inspection_id' => $inspection->id,
                            'inspection_question_id' => $question->id,
                            'score' => $i === 0 ? 4 : 8,
                            'passed' => $i !== 0,
                        ]);
                    }

                    if ($i === 0) {
                        CorrectiveAction::query()->create([
                            'tenant_id' => $tenant->id,
                            'inspection_id' => $inspection->id,
                            'cleaning_job_id' => $job->id,
                            'title' => 'Rework bathroom sanitation',
                            'description' => 'Re-clean and schedule reinspection.',
                            'status' => 'open',
                            'due_at' => now()->addDay(),
                        ]);
                    }
                }

                if ($i < 10) {
                    $invoice = Invoice::query()->create([
                        'tenant_id' => $tenant->id,
                        'customer_id' => $booking->customer_id,
                        'booking_id' => $booking->id,
                        'number' => 'INV-'.str_pad((string) (($index * 100) + $i + 1), 6, '0', STR_PAD_LEFT),
                        'status' => $i < 6 ? InvoiceStatus::Paid : InvoiceStatus::Unpaid,
                        'issue_date' => now()->subDays(10 - $i),
                        'due_date' => now()->addDays($i),
                        'subtotal' => $booking->subtotal,
                        'tax_total' => $booking->tax_total,
                        'discount_total' => 0,
                        'total' => $booking->total,
                        'amount_paid' => $i < 6 ? $booking->total : 0,
                        'balance_due' => $i < 6 ? 0 : $booking->total,
                        'currency' => 'USD',
                    ]);

                    InvoiceItem::query()->create([
                        'tenant_id' => $tenant->id,
                        'invoice_id' => $invoice->id,
                        'description' => 'Cleaning services',
                        'quantity' => 1,
                        'unit_price' => $booking->subtotal,
                        'tax_rate' => 8,
                        'line_total' => $booking->total,
                    ]);

                    if ($i < 6) {
                        Payment::query()->create([
                            'tenant_id' => $tenant->id,
                            'invoice_id' => $invoice->id,
                            'customer_id' => $invoice->customer_id,
                            'reference' => 'PAY-'.$invoice->number,
                            'method' => 'card',
                            'gateway' => 'manual',
                            'status' => PaymentStatus::Completed,
                            'amount' => $invoice->total,
                            'currency' => 'USD',
                            'paid_at' => now()->subDays(5),
                        ]);
                    }
                }
            }

            Contract::query()->create([
                'tenant_id' => $tenant->id,
                'customer_id' => $customers->first()->id,
                'branch_id' => $branches->first()->id,
                'reference' => 'CT-001',
                'title' => 'Annual Office Janitorial Agreement',
                'status' => 'active',
                'start_date' => now()->subMonth(),
                'end_date' => now()->addYear(),
                'billing_frequency' => 'monthly',
                'contract_value' => 24000,
                'sla' => ['response_hours' => 4, 'inspection_frequency' => 'weekly'],
                'scope' => ['nightly office clean', 'restroom sanitation'],
            ]);

            $quote = Quotation::query()->create([
                'tenant_id' => $tenant->id,
                'customer_id' => $customers->get(1)->id,
                'reference' => 'QT-001',
                'status' => 'sent',
                'subtotal' => 1200,
                'tax_total' => 96,
                'discount_total' => 0,
                'total' => 1296,
                'valid_until' => now()->addDays(14),
            ]);
            QuotationItem::query()->create([
                'tenant_id' => $tenant->id,
                'quotation_id' => $quote->id,
                'service_id' => $services->first()->id,
                'description' => 'Deep cleaning package',
                'quantity' => 1,
                'unit_price' => 1200,
                'line_total' => 1200,
            ]);

            Warehouse::query()->create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branches->first()->id,
                'name' => 'Main Supply Room',
                'code' => 'WH1',
                'is_active' => true,
            ]);

            foreach (['Disinfectant', 'Microfiber Cloths', 'Trash Bags', 'Floor Cleaner', 'Gloves', 'Glass Cleaner'] as $i => $item) {
                InventoryItem::query()->create([
                    'tenant_id' => $tenant->id,
                    'sku' => 'SKU-'.($i + 1),
                    'name' => $item,
                    'category' => 'consumables',
                    'unit' => 'pcs',
                    'quantity_on_hand' => 20 + $i * 5,
                    'reorder_level' => 10,
                    'unit_cost' => 5 + $i,
                    'is_active' => true,
                ]);
            }

            Supplier::query()->create([
                'tenant_id' => $tenant->id,
                'name' => 'CleanSupply Wholesale',
                'email' => 'orders@cleansupply.test',
                'phone' => '+1-555-8800',
                'contact_person' => 'Sam Supplier',
                'is_active' => true,
            ]);

            Asset::query()->create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branches->first()->id,
                'name' => 'Industrial Vacuum',
                'asset_tag' => 'AST-001',
                'serial_number' => 'VAC-99821',
                'category' => 'equipment',
                'status' => 'available',
                'purchase_cost' => 850,
                'next_maintenance_at' => now()->addMonth(),
            ]);

            $expenseCategory = ExpenseCategory::query()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Job Supplies',
            ]);
            Expense::query()->create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branches->first()->id,
                'expense_category_id' => $expenseCategory->id,
                'title' => 'Emergency supply run',
                'amount' => 86.50,
                'status' => 'approved',
                'expense_date' => now()->subDay(),
            ]);
        }

        Faq::query()->insert([
            ['question' => 'Can modules be disabled without losing data?', 'answer' => 'Yes. Deactivation hides navigation, routes, automations, and APIs while preserving historical records.', 'category' => 'modules', 'locale' => 'en', 'sort_order' => 1, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Is AI required?', 'answer' => 'No. AI features are optional and only run when providers and keys are configured.', 'category' => 'ai', 'locale' => 'en', 'sort_order' => 2, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Does the platform support multilingual PDFs?', 'answer' => 'Yes via the Unicode PDF package with RTL-aware rendering where supported.', 'category' => 'docs', 'locale' => 'en', 'sort_order' => 3, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Testimonial::query()->insert([
            ['customer_name' => 'Amina Rahman', 'company' => 'Northline Facilities', 'quote' => 'Inspection scores and crew utilization finally live in one place.', 'rating' => 5, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['customer_name' => 'Carlos Mendez', 'company' => 'BrightHome Cleaning', 'quote' => 'Scheduling conflicts dropped dramatically after go-live.', 'rating' => 5, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        BlogPost::query()->create([
            'slug' => 'quality-first-janitorial-operations',
            'title' => 'Quality-first janitorial operations',
            'locale' => 'en',
            'excerpt' => 'How failed inspections become corrective workflows.',
            'content' => 'Modern cleaning companies win contracts with measurable quality. CleanOps turns inspection failures into assigned rework and reinspection history.',
            'author' => 'CleanOps Team',
            'is_published' => true,
            'published_at' => now()->subDays(3),
        ]);

        Tenancy::forget();
    }

    protected function seedPrimaryTenantUsers(Tenant $tenant, Branch $branch, string $password): void
    {
        $users = [
            ['email' => 'owner@cleaningdemo.test', 'name' => 'Business Owner', 'role' => 'business_owner', 'type' => 'business_owner'],
            ['email' => 'operations@cleaningdemo.test', 'name' => 'Operations Manager', 'role' => 'operations_manager', 'type' => 'operations_manager'],
            ['email' => 'branch@cleaningdemo.test', 'name' => 'Branch Manager', 'role' => 'branch_manager', 'type' => 'branch_manager'],
            ['email' => 'inspector@cleaningdemo.test', 'name' => 'Quality Inspector', 'role' => 'quality_inspector', 'type' => 'quality_inspector'],
            ['email' => 'supervisor@cleaningdemo.test', 'name' => 'Cleaning Supervisor', 'role' => 'cleaning_supervisor', 'type' => 'cleaning_supervisor'],
            ['email' => 'cleaner@cleaningdemo.test', 'name' => 'Cleaning Staff', 'role' => 'cleaning_staff', 'type' => 'cleaning_staff'],
            ['email' => 'accountant@cleaningdemo.test', 'name' => 'Accountant', 'role' => 'accountant', 'type' => 'accountant'],
            ['email' => 'customer@cleaningdemo.test', 'name' => 'Demo Customer', 'role' => 'customer', 'type' => 'customer'],
        ];

        foreach ($users as $userData) {
            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'name' => $userData['name'],
                    'password' => Hash::make($password),
                    'user_type' => $userData['type'],
                    'is_platform_user' => false,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$userData['role']]);
        }

        $tenant->update([
            'owner_user_id' => User::query()->where('email', 'owner@cleaningdemo.test')->value('id'),
        ]);
    }
}
