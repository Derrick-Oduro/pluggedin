import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // The product intentionally ships with one considered light theme. Keeping
    // class-based variants available prevents legacy utility classes from
    // becoming system-theme rules while the remaining templates are refined.
    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'dark': {
                    DEFAULT: '#0F0F0F',
                    secondary: '#1A1A1A',
                },
                'light': {
                    DEFAULT: '#FFFFFF',
                    secondary: '#F5F5F5',
                },
                'brand': {
                    DEFAULT: '#B7481F',
                    deep: '#7A2B12',
                    light: '#D96035',
                    tint: '#FFF8F4',
                    soft: '#FCE4D8',
                },
                'orange': {
                    DEFAULT: '#B7481F',
                    dark: '#7A2B12',
                    light: '#D96035',
                    tint: '#FFF8F4',
                    soft: '#FCE4D8',
                },
                'text': {
                    primary: '#1C1917',
                    secondary: '#57534E',
                },
            },
        },
    },

    plugins: [forms],
};
