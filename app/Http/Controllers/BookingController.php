<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Service;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings()->with('service')->latest()->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Service $service)
    {
        abort_unless($service->is_active, 404);

        return view('bookings.create', compact('service'));
    }

    public function store(Request $request)
    {
        $service = Service::findOrFail($request->input('service_id'));
        abort_unless($service->is_active, 422, 'This service is no longer offered.');

        // Only device types the service covers (any, when it lists none).
        $allowedDevices = $service->device_types ?: array_keys(Service::DEVICE_TYPES);

        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'device_type' => ['required', \Illuminate\Validation\Rule::in($allowedDevices)],
            'device_model' => 'required|string|max:120',
            'contact_phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+ ()-]{9,}$/'],
            'handover_method' => 'required|in:drop_off,pickup',
            'pickup_location' => 'required_if:handover_method,pickup|nullable|string|max:255',
            'preferred_date' => 'required|date|after:today|before:+3 months',
            'notes' => [$service->type === 'diagnosis' ? 'required' : 'nullable', 'string', 'max:2000'],
        ], [
            'device_type.in' => 'This service isn’t available for that kind of device.',
            'contact_phone.regex' => 'Enter a phone number we can call, e.g. 024 123 4567.',
            'pickup_location.required_if' => 'Tell us where to collect the device.',
            'notes.required' => 'Describe what’s wrong so we know what to check.',
        ]);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $service->id,
            'device_type' => $data['device_type'],
            'device_model' => $data['device_model'],
            'contact_phone' => $data['contact_phone'],
            'handover_method' => $data['handover_method'],
            'pickup_location' => $data['handover_method'] === 'pickup' ? $data['pickup_location'] : null,
            'preferred_date' => $data['preferred_date'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        BookingStatusHistory::create([
            'booking_id' => $booking->id,
            'changed_by' => auth()->id(),
            'from_status' => null,
            'to_status' => 'pending',
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking sent. We’ll call '.$booking->contact_phone.' to confirm the time and price.');
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['service', 'statusHistories.actor']);

        return view('bookings.show', compact('booking'));
    }
}
