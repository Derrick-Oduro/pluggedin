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
        return view('bookings.create', compact('service'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'device_model' => 'required|string',
            'preferred_date' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'device_model' => $request->device_model,
            'preferred_date' => $request->preferred_date,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        BookingStatusHistory::create([
            'booking_id' => $booking->id,
            'changed_by' => auth()->id(),
            'from_status' => null,
            'to_status' => 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking request submitted successfully!');
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
