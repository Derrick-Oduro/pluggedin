<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 font-semibold text-gray-700 transition hover:border-orange hover:text-orange focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2 dark:border-gray-700 dark:bg-dark dark:text-text-secondary dark:hover:border-orange dark:hover:text-orange dark:focus:ring-offset-dark disabled:opacity-50']) }}>
    {{ $slot }}
</button>
