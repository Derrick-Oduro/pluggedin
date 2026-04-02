<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-orange px-5 py-2.5 font-semibold text-white transition hover:bg-orange-light focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2 dark:focus:ring-offset-dark']) }}>
    {{ $slot }}
</button>
