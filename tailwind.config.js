import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Charter', 'Georgia', 'serif'],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                gray: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            lineHeight: {
                'relaxed': '1.75',
                'loose': '2',
            },
            typography: {
                DEFAULT: {
                    css: {
                        maxWidth: 'none',
                        color: '#334155',
                        lineHeight: '1.75',
                        fontSize: '1.125rem',
                        p: {
                            marginTop: '1.5rem',
                            marginBottom: '1.5rem',
                        },
                        h1: {
                            color: '#0f172a',
                            fontWeight: '700',
                            fontSize: '2.25rem',
                            lineHeight: '1.2',
                            marginTop: '2rem',
                            marginBottom: '1rem',
                        },
                        h2: {
                            color: '#0f172a',
                            fontWeight: '600',
                            fontSize: '1.875rem',
                            lineHeight: '1.3',
                            marginTop: '1.75rem',
                            marginBottom: '0.75rem',
                        },
                        h3: {
                            color: '#0f172a',
                            fontWeight: '600',
                            fontSize: '1.5rem',
                            lineHeight: '1.4',
                            marginTop: '1.5rem',
                            marginBottom: '0.5rem',
                        },
                        blockquote: {
                            borderLeftColor: '#0ea5e9',
                            borderLeftWidth: '4px',
                            paddingLeft: '1.5rem',
                            fontStyle: 'italic',
                            color: '#475569',
                        },
                        code: {
                            backgroundColor: '#f1f5f9',
                            color: '#e11d48',
                            padding: '0.25rem 0.375rem',
                            borderRadius: '0.25rem',
                            fontSize: '0.875rem',
                        },
                        'code::before': {
                            content: '""',
                        },
                        'code::after': {
                            content: '""',
                        },
                        pre: {
                            backgroundColor: '#0f172a',
                            color: '#e2e8f0',
                            borderRadius: '0.5rem',
                            padding: '1.5rem',
                            overflow: 'auto',
                        },
                        'pre code': {
                            backgroundColor: 'transparent',
                            color: 'inherit',
                            padding: '0',
                        },
                        a: {
                            color: '#0ea5e9',
                            textDecoration: 'underline',
                            fontWeight: '500',
                        },
                        'a:hover': {
                            color: '#0284c7',
                        },
                    },
                },
            },
        },
    },

    plugins: [forms, typography],
};
