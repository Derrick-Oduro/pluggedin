<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-3xl">
            <x-page-header badge="Legal" title="Privacy policy" subtitle="Last updated {{ \Illuminate\Support\Carbon::parse('2026-11-19')->format('F j, Y') }}" />

            <article class="backend-card space-y-6 text-sm leading-7 text-stone-700 sm:p-8 [&_h2]:text-lg [&_h2]:font-extrabold [&_h2]:text-stone-950 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-5">
                <p>{{ $site['site_title'] }} respects your privacy and handles personal data in line with Ghana's Data Protection Act, 2012 (Act 843). This policy explains what we collect and why.</p>

                <section>
                    <h2>What we collect</h2>
                    <ul>
                        <li><strong>Account details:</strong> name, email address and password (stored only as a secure hash).</li>
                        <li><strong>Order details:</strong> items bought, delivery address or pickup campus, and phone number.</li>
                        <li><strong>Payment records:</strong> Paystack's transaction reference, amount and status. Card and mobile-money details are handled by Paystack and never reach us.</li>
                        <li><strong>Listings, reviews, bookings and trade-ins:</strong> the descriptions and photos you submit. Photos are resized and their hidden metadata, including location, is removed when uploaded.</li>
                        <li><strong>Technical data:</strong> session cookies needed to keep you logged in and your cart working, and basic server logs (such as IP address) for security.</li>
                    </ul>
                </section>

                <section>
                    <h2>How we use it</h2>
                    <ul>
                        <li>To run your account, process and deliver orders, handle repairs and trade-ins, and provide support.</li>
                        <li>To review listings and keep the marketplace safe from fraud and abuse.</li>
                        <li>To send account emails such as password resets, and in-app notifications you can switch off in Settings.</li>
                    </ul>
                    <p class="mt-2">We do not sell your personal data or use it for third-party advertising.</p>
                </section>

                <section>
                    <h2>Who we share it with</h2>
                    <ul>
                        <li><strong>Paystack</strong>, to take payments.</li>
                        <li><strong>Our hosting and email providers</strong>, who store and send data on our behalf.</li>
                        <li><strong>Other users</strong>, only where needed: your name appears on your public listings and reviews.</li>
                        <li><strong>Authorities</strong>, when required by law or to investigate fraud or stolen goods.</li>
                    </ul>
                </section>

                <section>
                    <h2>How long we keep it</h2>
                    <p>We keep account data while your account is open. Order and payment records are kept for as long as needed for accounting and legal obligations. You can delete your account from your profile page at any time.</p>
                </section>

                <section>
                    <h2>Your rights</h2>
                    <p>You can ask to see, correct, or delete the personal data we hold about you, or object to how we use it. Email <a href="mailto:{{ $site['contact_email'] }}" class="font-bold text-brand">{{ $site['contact_email'] }}</a>. If you are not satisfied with our response, you can complain to Ghana's Data Protection Commission.</p>
                </section>

                <p class="border-t border-stone-100 pt-4 text-xs text-stone-500">See also our <a href="{{ route('legal.terms') }}" class="font-bold text-brand">Terms of use</a>.</p>
            </article>
        </div>
    </section>
</x-app-layout>
