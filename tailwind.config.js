import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    theme: {
        extend: {
             colors: {
                'note-primary': '#3b82f6',
                'note-secondary': '#10b981',
                'note-accent': '#8b5cf6',
            },
            fontFamily: {
                'sans': ['Figtree', 'system-ui', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
