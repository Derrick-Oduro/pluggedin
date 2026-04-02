@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 transition focus:border-orange focus:ring-orange dark:border-gray-700 dark:bg-dark dark:text-text-primary']) }}>
