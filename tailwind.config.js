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
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Source Serif 4"', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                news: {
                    ink: '#0a0a0a',
                    muted: '#5c5c5c',
                    line: '#e5e5e5',
                    paper: '#fafafa',
                    accent: '#b91c1c',
                },
                // Admin nav/forms: brand accent (same as news.accent)
                primary: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#b91c1c',
                    700: '#991b1b',
                    800: '#7f1d1d',
                    900: '#450a0a',
                },
            },
            typography: ({ theme }) => ({
                DEFAULT: {
                    css: {
                        '--tw-prose-body': theme('colors.news.ink'),
                        '--tw-prose-headings': theme('colors.news.ink'),
                        '--tw-prose-links': theme('colors.news.accent'),
                        '--tw-prose-bold': theme('colors.news.ink'),
                        '--tw-prose-quotes': theme('colors.news.ink'),
                        '--tw-prose-quote-borders': theme('colors.news.accent'),
                        '--tw-prose-bullets': theme('colors.news.accent'),
                        '--tw-prose-counters': theme('colors.news.accent'),
                        maxWidth: 'none',
                        lineHeight: '1.8',
                        a: {
                            textDecoration: 'underline',
                            fontWeight: '500',
                            '&:hover': {
                                color: theme('colors.news.ink'),
                            },
                        },
                        img: {
                            marginTop: '1.5em',
                            marginBottom: '1.5em',
                        },
                    },
                },
            }),
        },
    },

    plugins: [forms, typography],
};
