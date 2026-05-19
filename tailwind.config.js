/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class', // ← AJOUTÉ : active le mode sombre via la classe "dark" sur <html>
  theme: {
    extend: {
      colors: {
        // Palette élégante & épurée
        primary: '#1E40AF',      // Bleu profond (confiance)
        primaryLight: '#3B82F6', // Bleu clair (hover)
        secondary: '#0F172A',    // Gris anthracite (texte/footer)
        accent: '#F59E0B',       // Ambre (CTA, promotions)
        background: '#F8FAFC',   // Blanc cassé (fond doux)
        card: '#FFFFFF',         // Blanc pur (cartes)
        border: '#E2E8F0',       // Gris très clair (séparateurs)
        success: '#10B981',
        danger: '#EF4444',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        'xs': ['0.7rem', '1rem'],
        'sm': ['0.8rem', '1.15rem'],
        'base': ['0.9rem', '1.35rem'],
        'lg': ['1rem', '1.5rem'],
        'xl': ['1.125rem', '1.625rem'],
        '2xl': ['1.375rem', '1.875rem'],
        '3xl': ['1.625rem', '2.125rem'],
      },
      spacing: {
        'card': '0.875rem',
        'section': '2.5rem',
      },
      borderRadius: {
        'card': '0.75rem',
        'btn': '0.5rem',
      },
      boxShadow: {
        'card': '0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04)',
        'card-hover': '0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05)',
        'btn': '0 2px 4px rgba(30,64,175,0.2)',
      },
      container: {
        center: true,
        padding: '1rem',
        screens: {
          sm: '640px',
          md: '768px',
          lg: '1024px',
          xl: '1280px',
          '2xl': '1440px',
        },
      },
    },
  },
  plugins: [],
}