import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                "primary": "#004532",
                "primary": "#004532",
                "surface": "#f7f9fb",
                "on-surface": "#191c1e",
                "on-surface-variant": "#3f4944",
                "primary-container": "#065f46",
                "on-primary-container": "#8bd6b7",
                "error": "#ba1a1a",
                secondary: {
                    DEFAULT: '#e9d736ff', // Ganti dengan kode hex warna secondary ARTHADATA Anda
                    dark: '#475569',    // Warna untuk hover:bg-secondary-dark
                },
            },
            fontFamily: {
                headline: ['Lexend', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/typography'),
    ],
};