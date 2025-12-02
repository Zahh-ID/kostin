/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./index.html",
        "./src/**/*.{js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#ccff00',
                    dim: '#b3e600',
                    glow: 'rgba(204, 255, 0, 0.4)',
                },
                secondary: {
                    DEFAULT: '#00f0ff',
                    dim: '#00c2cc',
                },
                bg: '#0a0a0b',
                surface: {
                    DEFAULT: '#121214',
                    highlight: '#1c1c1f',
                },
                text: {
                    DEFAULT: '#ffffff',
                    secondary: '#a1a1aa',
                    tertiary: '#71717a',
                },
                border: 'rgba(255, 255, 255, 0.08)',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                display: ['"Space Grotesk"', 'sans-serif'],
            },
            container: {
                center: true,
                padding: '1.5rem',
                screens: {
                    sm: '640px',
                    md: '768px',
                    lg: '1024px',
                    xl: '1280px',
                },
            },
        },
    },
    plugins: [],
}
