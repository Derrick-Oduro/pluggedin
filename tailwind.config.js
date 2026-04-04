import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
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
                'orange': {
                    DEFAULT: '#B02F00',
                    dark: '#8F2500',
                    light: '#FF5722',
                },
                'text': {
                    primary: '#FFFFFF',
                    secondary: '#BDBDBD',
                },
            },
        },
    },

    plugins: [forms],
};
