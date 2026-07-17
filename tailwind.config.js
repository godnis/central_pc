import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Sora', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Verde-PCB: a cor da placa de circuito, identidade da marca.
                brand: {
                    50: '#eff8f4',
                    100: '#d7eee3',
                    200: '#a9dcc5',
                    300: '#6fc2a0',
                    400: '#3aa47c',
                    500: '#1f8862',
                    600: '#156b4e',
                    700: '#11553f',
                    800: '#0e4434',
                    900: '#0b372b',
                    950: '#05231b',
                },
                // Cobre: a cor das trilhas, usada como acento pontual.
                copper: {
                    300: '#e8b48a',
                    400: '#d98e55',
                    500: '#c2703d',
                    600: '#a85a2c',
                },
            },
        },
    },

    plugins: [forms],
};
