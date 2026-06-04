import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                nacho: {
                    primary: '#B5471F',
                    'primary-dark': '#8F3414',
                    dark: '#2A2724',
                    cream: '#FAF6F0',
                    success: '#2E7D32',
                    warning: '#F59E0B',
                    danger: '#C62828',
                },
            },
        },
    },

    plugins: [forms],
};
