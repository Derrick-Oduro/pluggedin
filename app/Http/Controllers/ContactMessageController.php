<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'Thanks — your message has been received. We will be in touch soon.');
    }

    public function index(Request $request): View
    {
        $tabCounts = [
            'new' => ContactMessage::whereNull('handled_at')->count(),
            'handled' => ContactMessage::whereNotNull('handled_at')->count(),
            'all' => ContactMessage::count(),
        ];
        $tab = array_key_exists($request->query('tab'), $tabCounts) ? $request->query('tab') : 'new';

        $messages = ContactMessage::with('handler')
            ->when($tab === 'new', fn ($q) => $q->whereNull('handled_at')->oldest())
            ->when($tab === 'handled', fn ($q) => $q->whereNotNull('handled_at')->latest('handled_at'))
            ->when($tab === 'all', fn ($q) => $q->latest())
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.contact-messages.index', compact('messages', 'tab', 'tabCounts'));
    }

    /** Mark a message as dealt with, or reopen it. */
    public function update(Request $request, ContactMessage $message): RedirectResponse
    {
        $handled = $request->boolean('handled');
        $message->update(['handled_at' => $handled ? now() : null, 'handled_by' => $handled ? $request->user()->id : null]);
        \App\Support\AuditLogger::log('superadmin.message.'.($handled ? 'handled' : 'reopened'), ContactMessage::class, $message->id, ['subject' => $message->subject, 'email' => $message->email], $request);

        return back()->with('success', $handled ? "Message from {$message->name} marked as handled." : "Message from {$message->name} moved back to New.");
    }
}
