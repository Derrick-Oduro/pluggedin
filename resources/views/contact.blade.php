<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Contact PluggedIn" title="Let's talk about your next upgrade" subtitle="Questions, service bookings, or upgrade advice. Reach out and we will help you choose the best path for your device and budget." />

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white/90 border border-stone-200/70 rounded-2xl p-7 shadow-sm">
                        <h2 class="text-lg font-extrabold text-stone-950 mb-6">Get in Touch</h2>

                        <div class="space-y-5">
                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-xl bg-brand/15 text-brand flex items-center justify-center shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.91l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 9.653a2.25 2.25 0 0 1-1.07-1.91V7.5" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Email</h3>
                                    <a href="mailto:support@pluggedin.com" class="text-brand hover:text-brand-deep font-medium">support@pluggedin.com</a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-xl bg-brand/15 text-brand flex items-center justify-center shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 4.5A2.25 2.25 0 0 1 4.5 2.25h3A2.25 2.25 0 0 1 9.75 4.5v2.068a2.25 2.25 0 0 1-1.318 2.048l-1.17.52a11.035 11.035 0 0 0 7.602 7.602l.52-1.17a2.25 2.25 0 0 1 2.048-1.318H19.5a2.25 2.25 0 0 1 2.25 2.25v3A2.25 2.25 0 0 1 19.5 21.75h-1.5C9.302 21.75 2.25 14.698 2.25 6V4.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Phone / WhatsApp</h3>
                                    <a href="tel:+233594042521" class="text-brand hover:text-brand-deep font-medium">+233 594 042 521</a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-xl bg-brand/15 text-brand flex items-center justify-center shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m6.75 0A9.75 9.75 0 1 1 3.25 12a9.75 9.75 0 0 1 19.5 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Business Hours</h3>
                                    <p class="text-stone-600 leading-7">
                                        Monday - Friday: 9:00 AM - 6:00 PM<br>
                                        Saturday: 10:00 AM - 4:00 PM<br>
                                        Sunday: Closed
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-brand to-brand-deep text-white rounded-2xl p-7 shadow-lg shadow-brand/20">
                        <h3 class="text-xl font-bold mb-2">Quick Response Promise</h3>
                        <p class="text-white/90">Most inquiries are answered within one business day. For urgent repairs, include your device model and issue in the message.</p>
                    </div>
                </div>

                <div class="lg:col-span-3 backend-card sm:p-8">
                    <h2 class="text-3xl font-bold mb-2">Send a Message</h2>
                    <p class="text-stone-600 mb-8">Tell us what device you have and what you want to improve. We will guide you from there.</p>

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-bold text-stone-800">Name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" placeholder="Your name" class="backend-field">
                                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-bold text-stone-800">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com" class="backend-field">
                                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="mb-1.5 block text-sm font-bold text-stone-800">Subject</label>
                            <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required placeholder="Upgrade consultation, repair booking, etc." class="backend-field">
                            @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="message" class="mb-1.5 block text-sm font-bold text-stone-800">Message</label>
                            <textarea id="message" name="message" rows="6" required placeholder="Share your device model, current issue, and what you are hoping to achieve." class="backend-field">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="btn-primary sm:w-auto w-full">
                            Send Message
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L6 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
