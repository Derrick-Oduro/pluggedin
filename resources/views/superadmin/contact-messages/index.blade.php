<x-superadmin-layout>
    <x-slot name="header">Contact messages</x-slot>

    <div class="mb-6">
        <p class="text-sm text-stone-600">Messages submitted through the public contact form.</p>
    </div>

    <div class="backend-table-wrap">
        <table class="backend-table">
            <thead><tr><th class="backend-th">Sender</th><th class="backend-th">Message</th><th class="backend-th">Received</th></tr></thead>
            <tbody>
                @forelse($messages as $message)
                    <tr class="backend-row">
                        <td class="backend-td"><p class="font-bold text-stone-900">{{ $message->name }}</p><a href="mailto:{{ $message->email }}" class="text-xs text-brand-deep hover:underline">{{ $message->email }}</a></td>
                        <td class="backend-td"><p class="font-bold text-stone-900">{{ $message->subject }}</p><p class="mt-1 max-w-2xl whitespace-pre-line text-sm leading-6 text-stone-600">{{ $message->message }}</p></td>
                        <td class="backend-td whitespace-nowrap text-stone-500">{{ $message->created_at->format('M j, Y') }}<br><span class="text-xs">{{ $message->created_at->format('g:i A') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="backend-td py-12 text-center text-stone-500">No contact messages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $messages->links() }}</div>
</x-superadmin-layout>
