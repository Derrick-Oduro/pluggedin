<x-superadmin-layout title="Messages">
    <div class="space-y-5">
        <x-page-header badge="To do" title="Messages" subtitle="Questions from the contact form. Reply by email, then mark the message as handled so it leaves the queue." />

        <x-admin-tabs route="superadmin.contact-messages.index" :current="$tab" :counts="$tabCounts"
            :tabs="['new' => 'New', 'handled' => 'Handled', 'all' => 'All']" />

        <div class="space-y-3">
            @forelse($messages as $message)
                @php
                    $mailto = 'mailto:'.$message->email.'?subject='.rawurlencode('Re: '.$message->subject)
                        .'&body='.rawurlencode("Hi {$message->name},\n\n\n\n---\nYou wrote on ".$message->created_at->format('M j').":\n> ".Str::limit($message->message, 300));
                    $long = Str::length($message->message) > 320;
                @endphp
                <article class="rounded-2xl border bg-white {{ $message->handled_at ? 'border-stone-200' : 'border-l-4 border-stone-200 border-l-brand' }}" x-data="{ full: false }">
                    <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-start">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $message->handled_at ? 'bg-stone-100 text-stone-500' : 'bg-brand-tint text-brand-deep' }} text-sm font-extrabold">{{ Str::upper(Str::substr($message->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                <p class="font-extrabold text-stone-950">{{ $message->subject }}</p>
                                <p class="text-xs text-stone-500" title="{{ $message->created_at->format('M j, Y g:i A') }}">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-stone-600">{{ $message->name }} · <a href="mailto:{{ $message->email }}" class="text-brand-deep hover:underline">{{ $message->email }}</a></p>
                            <div class="mt-3 text-sm leading-6 text-stone-800">
                                @if($long)
                                    <p class="whitespace-pre-line" x-show="!full">{{ Str::limit($message->message, 320) }}</p>
                                    <p class="whitespace-pre-line" x-cloak x-show="full">{{ $message->message }}</p>
                                    <button type="button" class="mt-1 block text-xs font-bold text-stone-500 hover:text-brand-deep" x-on:click="full = !full" x-text="full ? 'Show less' : 'Read all'">Read all</button>
                                @else
                                    <p class="whitespace-pre-line">{{ $message->message }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 border-t border-stone-100 bg-stone-50/70 px-5 py-3">
                        <a href="{{ $mailto }}" class="backend-btn-primary h-10">Reply by email</a>
                        <form method="POST" action="{{ route('superadmin.contact-messages.update', $message) }}">
                            @csrf @method('PATCH')
                            @if($message->handled_at)
                                <input type="hidden" name="handled" value="0">
                                <button class="btn-secondary h-10 px-4">Reopen</button>
                            @else
                                <input type="hidden" name="handled" value="1">
                                <button class="btn-secondary h-10 px-4">Mark handled</button>
                            @endif
                        </form>
                        @if($message->handled_at)
                            <span class="ml-auto text-xs text-stone-500">Handled by {{ $message->handler?->name ?? 'an admin' }} · {{ $message->handled_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white py-14 text-center">
                    <p class="font-extrabold text-stone-950">{{ $tab === 'new' ? 'Inbox zero' : 'Nothing here' }}</p>
                    <p class="mt-1 text-sm text-stone-500">{{ $tab === 'new' ? 'Every message has been handled.' : 'No messages in this view.' }}</p>
                </div>
            @endforelse
        </div>

        <div>{{ $messages->links() }}</div>
    </div>
</x-superadmin-layout>
