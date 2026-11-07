<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::query()->where('is_active', true)->paginate(50);
    }

    public function show(Service $service)
    {
        return $service->load('category', 'addons');
    }
}
