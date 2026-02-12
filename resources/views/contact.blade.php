<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-5xl font-bold mb-8 text-center">Contact Us</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-dark-secondary rounded-lg p-8">
                <h2 class="text-2xl font-bold mb-6">Get in Touch</h2>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold mb-2">📧 Email</h3>
                        <a href="mailto:support@pluggedin.com" class="text-orange hover:text-orange-light">
                            support@pluggedin.com
                        </a>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2">📱 Phone / WhatsApp</h3>
                        <a href="tel:+1234567890" class="text-orange hover:text-orange-light">
                            +233 594 042 521
                        </a>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2">⏰ Business Hours</h3>
                        <p class="text-text-secondary">
                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-dark-secondary rounded-lg p-8">
                <h2 class="text-2xl font-bold mb-6">Send a Message</h2>

                <form class="space-y-4">
                    <div>
                        <label class="block text-sm mb-2">Name</label>
                        <input type="text" class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    </div>

                    <div>
                        <label class="block text-sm mb-2">Email</label>
                        <input type="email" class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    </div>

                    <div>
                        <label class="block text-sm mb-2">Message</label>
                        <textarea rows="4" class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
