<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Notifications\BookingStatusUpdatedNotification;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'service'])->latest()->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'service', 'statusHistories.actor']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed'
        ]);

        $previousStatus = $booking->status;
        $newStatus = $request->status;

        $booking->update(['status' => $newStatus]);

        if ($previousStatus !== $newStatus) {
            $booking->loadMissing('user');
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'changed_by' => auth()->id(),
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
            ]);
            $booking->user?->notify(new BookingStatusUpdatedNotification($booking->id, $previousStatus, $newStatus));
        }

        return redirect()->back()->with('success', 'Booking status updated successfully!');
    }
}
