<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Super admin: the workshop menu shown on the public Repairs page. */
class ServiceManagementController extends Controller
{
    public function index()
    {
        $services = Service::ordered()
            ->with('partsCategory')
            ->withCount(['bookings', 'bookings as open_bookings_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved'])])
            ->get()
            ->groupBy('type');

        return view('superadmin.services.index', [
            'groups' => collect(Service::TYPES)->mapWithKeys(fn ($label, $type) => [$type => $services->get($type, collect())]),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) Service::where('type', $data['type'])->max('sort_order') + 1;
        $service = Service::create($data);
        AuditLogger::log('superadmin.service.created', Service::class, $service->id, ['name' => $service->name, 'price' => $service->priceLabel()], $request);

        return back()->with('success', "“{$service->name}” added".($service->is_active ? ' and shown on the Repairs page.' : ' (hidden).'));
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request, $service));
        AuditLogger::log('superadmin.service.updated', Service::class, $service->id, ['name' => $service->name, 'price' => $service->priceLabel()], $request);

        return back()->with('success', "“{$service->name}” saved.");
    }

    public function toggle(Request $request, Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);
        AuditLogger::log('superadmin.service.'.($service->is_active ? 'shown' : 'hidden'), Service::class, $service->id, ['name' => $service->name], $request);

        return back()->with('success', $service->is_active ? "“{$service->name}” is back on the Repairs page." : "“{$service->name}” is hidden. Existing bookings aren't affected.");
    }

    /** Move a service up or down within its group. */
    public function move(Request $request, Service $service)
    {
        $direction = $request->validate(['direction' => 'required|in:up,down'])['direction'];
        $siblings = Service::where('type', $service->type)->ordered()->get()->values();
        $index = $siblings->search(fn ($s) => $s->id === $service->id);
        $swap = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && isset($siblings[$swap])) {
            $order = $siblings->pluck('id')->all();
            [$order[$index], $order[$swap]] = [$order[$swap], $order[$index]];
            foreach ($order as $position => $id) {
                Service::whereKey($id)->update(['sort_order' => $position]);
            }
        }

        return back();
    }

    public function destroy(Request $request, Service $service)
    {
        if ($service->bookings()->exists()) {
            return back()->with('error', "“{$service->name}” has bookings, so it can't be deleted. Hide it instead.");
        }

        $service->delete();
        AuditLogger::log('superadmin.service.deleted', Service::class, $service->id, ['name' => $service->name], $request);

        return back()->with('success', "“{$service->name}” deleted.");
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('services', 'name')->ignore($service?->id)],
            'type' => ['required', Rule::in(array_keys(Service::TYPES))],
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0|max:100000',
            'price_is_from' => 'nullable|boolean',
            'turnaround' => 'nullable|string|max:60',
            'device_types' => 'nullable|array',
            'device_types.*' => Rule::in(array_keys(Service::DEVICE_TYPES)),
            'parts_category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ], ['name.unique' => 'There is already a service with this name.']);

        return array_merge($data, [
            'price_is_from' => $request->boolean('price_is_from'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($service?->is_active ?? true),
            'device_types' => array_values($data['device_types'] ?? []) ?: null,
            'parts_category_id' => $data['type'] === 'upgrade' ? ($data['parts_category_id'] ?? null) : null,
        ]);
    }
}
