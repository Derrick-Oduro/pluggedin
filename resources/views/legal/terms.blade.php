<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-3xl">
            <x-page-header badge="Legal" title="Terms of use" subtitle="Last updated {{ \Illuminate\Support\Carbon::parse('2026-11-19')->format('F j, Y') }}" />

            <article class="backend-card space-y-6 text-sm leading-7 text-stone-700 sm:p-8 [&_h2]:text-lg [&_h2]:font-extrabold [&_h2]:text-stone-950 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-5">
                <p>These terms govern your use of {{ $site['site_title'] }} ("we", "us"), a marketplace for buying, selling, trading in and repairing technology, operated in Ghana. By creating an account or placing an order you agree to them.</p>

                <section>
                    <h2>1. Your account</h2>
                    <ul>
                        <li>You must give accurate details and keep your password secret. You are responsible for activity on your account.</li>
                        <li>We may suspend accounts that break these terms, post fraudulent listings, or abuse other users.</li>
                    </ul>
                </section>

                <section>
                    <h2>2. Buying</h2>
                    <ul>
                        <li>Prices are in Ghana cedis (GH₵). Payment is processed by Paystack; we never see or store your full card or mobile-money details.</li>
                        <li>Items are reserved when you check out. If payment is not completed within about an hour, the order is cancelled automatically and the items are released.</li>
                        <li>You can cancel an unpaid order yourself. To cancel a paid order, contact us before it is dispatched or collected; approved refunds go back through Paystack to the original payment method.</li>
                        <li>For campus pickup, bring the order number. For delivery, make sure the address and phone number are correct.</li>
                    </ul>
                </section>

                <section>
                    <h2>3. Selling</h2>
                    <ul>
                        <li>You may only list items you own and are entitled to sell. Stolen, counterfeit, or illegal items are prohibited and may be reported to the authorities.</li>
                        <li>Listings must describe the item honestly, including its condition, specifications and any known faults. Phones and tablets must be free of iCloud/Google account locks unless clearly stated.</li>
                        <li>Every listing is reviewed before it goes live, and we may reject or remove listings at our discretion. Monthly upload limits apply.</li>
                    </ul>
                </section>

                <section>
                    <h2>4. Repairs, upgrades and trade-ins</h2>
                    <ul>
                        <li>Booking a service is a request; it is confirmed once we accept it. Back up your data before handing over a device. We are not responsible for data loss.</li>
                        <li>Trade-in quotes are based on the details and photos you provide and may change after we inspect the device.</li>
                    </ul>
                </section>

                <section>
                    <h2>5. Reviews and content</h2>
                    <p>Reviews must be honest and about your own experience. We may moderate or remove content that is abusive, misleading, or unrelated. You keep ownership of photos you upload, and grant us permission to display them on the site for your listing.</p>
                </section>

                <section>
                    <h2>6. Liability</h2>
                    <p>Community listings are sold by other users; we review them but cannot guarantee every claim a seller makes. To the extent allowed by Ghanaian law, our liability for any order is limited to the amount you paid for it. Nothing in these terms removes rights you have under consumer protection law.</p>
                </section>

                <section>
                    <h2>7. Changes and contact</h2>
                    <p>We may update these terms; the date at the top shows the latest version. Questions? Email <a href="mailto:{{ $site['contact_email'] }}" class="font-bold text-brand-deep">{{ $site['contact_email'] }}</a>@if($site['contact_phone']) or call {{ $site['contact_phone'] }}@endif.</p>
                </section>

                <p class="border-t border-stone-100 pt-4 text-xs text-stone-500">See also our <a href="{{ route('legal.privacy') }}" class="font-bold text-brand-deep">Privacy policy</a>.</p>
            </article>
        </div>
    </section>
</x-app-layout>
