<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\CmsPage;
use App\Models\Faq;
use App\Models\SubscriptionPlan;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\Modules\ModuleManager;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketingController extends Controller
{
    public function home()
    {
        return view('website.home', [
            'plans' => SubscriptionPlan::query()->where('is_public', true)->where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::query()->where('is_published', true)->latest()->take(6)->get(),
            'faqs' => Faq::query()->where('is_published', true)->orderBy('sort_order')->take(8)->get(),
            'modules' => app(ModuleManager::class)->definitions()->take(8),
        ]);
    }

    public function about()
    {
        return view('website.page', [
            'title' => 'About Us',
            'subtitle' => 'Built for cleaning operators who need scheduling, quality, and growth in one platform.',
            'content' => CmsPage::query()->where('slug', 'about')->value('content')
                ?? 'CleanOps is an enterprise cleaning and janitorial business operating system covering CRM, scheduling, workforce, inspections, billing, inventory, and analytics.',
        ]);
    }

    public function features()
    {
        return view('website.features', [
            'modules' => app(ModuleManager::class)->definitions(),
        ]);
    }

    public function modules()
    {
        return view('website.modules', [
            'modules' => app(ModuleManager::class)->definitions(),
        ]);
    }

    public function moduleShow(string $key)
    {
        $module = app(ModuleManager::class)->definition($key);
        abort_unless($module, 404);

        return view('website.module-show', compact('module'));
    }

    public function solutions()
    {
        return view('website.solutions');
    }

    public function solutionShow(string $slug)
    {
        $solutions = $this->solutionCatalog();
        abort_unless(isset($solutions[$slug]), 404);

        return view('website.solution-show', [
            'solution' => $solutions[$slug],
            'slug' => $slug,
        ]);
    }

    public function pricing()
    {
        return view('website.pricing', [
            'plans' => SubscriptionPlan::query()->where('is_public', true)->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function demo()
    {
        return view('website.demo');
    }

    public function demoSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'company' => 'nullable|string|max:120',
            'message' => 'nullable|string|max:2000',
        ]);

        return back()->with('success', 'Thanks! Our team will contact you to schedule a demo.');
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'message' => 'required|string|max:5000',
        ]);

        return back()->with('success', 'Message received. We will respond shortly.');
    }

    public function faq()
    {
        return view('website.faq', [
            'faqs' => Faq::query()->where('is_published', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function blog()
    {
        return view('website.blog', [
            'posts' => BlogPost::query()->where('is_published', true)->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(string $slug)
    {
        $post = BlogPost::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();

        return view('website.blog-show', compact('post'));
    }

    public function caseStudies()
    {
        return view('website.page', [
            'title' => 'Case Studies',
            'subtitle' => 'How cleaning companies modernize operations with CleanOps.',
            'content' => 'Commercial janitorial firms use CleanOps to cut missed jobs, raise inspection scores, and accelerate invoicing across multi-branch networks.',
        ]);
    }

    public function privacy()
    {
        return view('website.legal', ['title' => 'Privacy Policy', 'slug' => 'privacy']);
    }

    public function terms()
    {
        return view('website.legal', ['title' => 'Terms and Conditions', 'slug' => 'terms']);
    }

    public function cookies()
    {
        return view('website.legal', ['title' => 'Cookie Policy', 'slug' => 'cookies']);
    }

    public function security()
    {
        return view('website.page', [
            'title' => 'Security',
            'subtitle' => 'Tenant isolation, RBAC, audit trails, and secure API access.',
            'content' => 'CleanOps enforces tenant scoping, permission checks, CSRF/XSS protections, hashed credentials, signed webhook verification hooks, and private document storage.',
        ]);
    }

    public function status()
    {
        return view('website.status');
    }

    public function docs()
    {
        return view('website.page', [
            'title' => 'Documentation',
            'subtitle' => 'Product and technical guides for operators and developers.',
            'content' => 'See the repository docs/ folder for architecture, modules, API, installation, deployment, security, and user guides.',
        ]);
    }

    public function apiDocs()
    {
        return view('website.api-docs');
    }

    public function trial()
    {
        return view('website.trial', [
            'plans' => SubscriptionPlan::query()->where('is_public', true)->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function registerTenant(Request $request, TenantManager $tenants, ModuleManager $modules)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:160',
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'plan' => 'nullable|string',
        ]);

        $tenant = $tenants->create([
            'name' => $data['business_name'],
            'email' => $data['email'],
            'slug' => Str::slug($data['business_name']).'-'.Str::lower(Str::random(4)),
        ]);

        $user = User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => 'business_owner',
            'is_active' => true,
        ]);

        $tenant->update(['owner_user_id' => $user->id]);
        $user->assignRole('business_owner');

        $plan = $data['plan'] ?? 'professional';
        $planModules = [
            'starter' => ['crm', 'catalog', 'bookings', 'workforce', 'invoicing', 'payments', 'portal'],
            'professional' => ['crm', 'catalog', 'bookings', 'scheduling', 'workforce', 'jobs', 'quality', 'portal', 'contracts', 'quotations', 'invoicing', 'payments', 'inventory', 'reports', 'communications'],
            'business' => ['crm', 'catalog', 'bookings', 'scheduling', 'workforce', 'attendance', 'jobs', 'quality', 'portal', 'contracts', 'quotations', 'invoicing', 'payments', 'inventory', 'assets', 'procurement', 'hr', 'payroll', 'expenses', 'branches', 'reports', 'automation', 'communications', 'tickets'],
            'enterprise' => array_keys(config('modules.modules')),
        ];

        $modules->activateDefaults($tenant, $planModules[$plan] ?? $planModules['professional']);

        Auth::login($user);

        return redirect('/admin')->with('success', 'Welcome to CleanOps. Your trial workspace is ready.');
    }

    protected function solutionCatalog(): array
    {
        return [
            'residential' => ['title' => 'Residential Cleaning', 'body' => 'Bookings, recurring schedules, customer portal, and quality checks for home cleaning brands.'],
            'commercial' => ['title' => 'Commercial Cleaning', 'body' => 'Multi-site contracts, night crews, inspections, and SLA tracking for offices and facilities.'],
            'janitorial' => ['title' => 'Janitorial Contractors', 'body' => 'Dispatch, attendance, inventory consumption, and payroll-ready time logs.'],
            'healthcare' => ['title' => 'Healthcare Cleaning', 'body' => 'Disinfection workflows, strict checklists, and audit-ready inspection history.'],
            'hotel' => ['title' => 'Hotel Cleaning', 'body' => 'Room turns, shift planning, and quality scoring for hospitality operations.'],
            'school' => ['title' => 'School Cleaning', 'body' => 'Campus zones, seasonal deep cleans, and branch-level reporting.'],
            'industrial' => ['title' => 'Industrial Cleaning', 'body' => 'Equipment-heavy jobs, safety certifications, and asset maintenance schedules.'],
            'franchise' => ['title' => 'Franchise Solutions', 'body' => 'Central branding with local operations, royalties reporting, and permission boundaries.'],
        ];
    }
}
