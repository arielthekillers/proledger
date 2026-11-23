/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './views/**/*.php',
        './views/**/**/*.php',
        './helpers/**/*.php',
        './public/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#2563EB',
                    dark: '#1E40AF',
                    light: '#EFF6FF',
                },
                slate: {
                    850: '#1e293b',
                    900: '#0F172A',
                }
            }
        },
    },
    plugins: [],
};
