<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::active()->ordered()->with('partsCategory')->get()->groupBy('type');

        return view('services.index', [
            'diagnosis' => $services->get('diagnosis', collect())->first(),
            'repairs' => $services->get('repair', collect()),
            'upgrades' => $services->get('upgrade', collect()),
        ]);
    }

    public function show(Service $service)
    {
        abort_unless($service->is_active, 404);

        $service->load('partsCategory');

        return view('services.show', compact('service'));
    }
}
