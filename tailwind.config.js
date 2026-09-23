/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: {
                    DEFAULT: '#241811',
                    soft: '#6E5F4E',
                },
                marun: {
                    DEFAULT: '#7A2131',
                    dark: '#521521',
                    light: '#9C3A4B',
                },
                emas: {
                    DEFAULT: '#C0922C',
                    light: '#E3C578',
                    dim: '#8C6A20',
                },
                sand: {
                    DEFAULT: '#F1E9D6',
                    dark: '#E6DAB8',
                },
                hijau: {
                    DEFAULT: '#3C6E4B',
                    light: '#DCEADF',
                },
                garis: '#E1D3AE',
            },
            fontFamily: {
                display: ['"Fraunces"', 'serif'],
                sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'monospace'],
            },
            backgroundImage: {
                gonjong: "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='40' viewBox='0 0 120 40'%3E%3Cpath d='M0 40 C 10 10, 20 0, 30 20 C 40 0, 50 10, 60 40 C 70 10, 80 0, 90 20 C 100 0, 110 10, 120 40 Z' fill='%23C0922C' fill-opacity='0.16'/%3E%3C/svg%3E\")",
            },
        },
    },
    plugins: [],
};
