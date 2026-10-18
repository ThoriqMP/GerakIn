import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#F0F7FF',
                    100: '#E0EFFF',
                    200: '#BAE0FD',
                    300: '#7CC8FB',
                    400: '#36ACF6',
                    500: '#2563EB', // soft blue primary
                    600: '#1D4ED8',
                    700: '#1E40AF',
                    800: '#1E3A8A',
                    900: '#172554',
                },
                gerakin: {
                    blue: '#2563EB',       // Primary soft blue
                    softBlue: '#3B82F6',
                    lightBlue: '#EFF6FF',
                    borderBlue: '#DBEAFE',
                    navy: '#0F172A',
                    surface: '#FFFFFF',
                    bg: '#F8FAFC',
                    border: '#E2E8F0',
                    muted: '#64748B',
                    success: '#10B981',
                    danger: '#EF4444',
                    warning: '#F59E0B',
                }
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'sans-serif'],
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                'floating': '0 20px 40px -15px rgba(15, 23, 42, 0.12)',
                'soft-blue': '0 8px 25px -4px rgba(37, 99, 235, 0.25)',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
