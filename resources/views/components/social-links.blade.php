{{-- WhatsApp, Facebook, X and Instagram buttons from the super admin Settings. Empty ones are hidden. --}}
@php
    $site = \App\Support\SiteSettings::all();
    $whatsapp = preg_replace('/\D/', '', ($site['whatsapp_number'] ?? '') ?: ($site['contact_phone'] ?? ''));
    if (str_starts_with($whatsapp, '0')) {
        $whatsapp = '233'.substr($whatsapp, 1);
    }
    $links = array_filter([
        'WhatsApp' => $whatsapp ? 'https://wa.me/'.$whatsapp : null,
        'Facebook' => $site['facebook_url'] ?? null,
        'X (Twitter)' => $site['x_url'] ?? null,
        'Instagram' => $site['instagram_url'] ?? null,
    ]);
    $icons = [
        'WhatsApp' => 'M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.1.6a2.7 2.7 0 0 0 1.8-1.2c.2-.6.2-1.1.1-1.2l-.4-.3Z',
        'Facebook' => 'M14 8h3V4h-3a4 4 0 0 0-4 4v2H8v4h2v8h4v-8h3l1-4h-4V8.5c0-.3.2-.5.5-.5Z',
        'X (Twitter)' => 'M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.3-8.3L2 3h6.3l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.4 4.7H5.5l11.2 14.5Z',
        'Instagram' => 'M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9a5.5 5.5 0 0 1-5.5 5.5h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2Zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm5.3-3.3a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z',
    ];
@endphp
@if ($links)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
        @foreach ($links as $label => $url)
            <a href="{{ $url }}" target="_blank" rel="noopener" class="social-dot" aria-label="{{ $label }}" title="{{ $label }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{{ $icons[$label] }}" /></svg>
            </a>
        @endforeach
    </div>
@endif
