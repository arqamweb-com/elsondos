/** @type {import('tailwindcss').Config} */
export default {
  darkMode: "class", // Enables dark mode using a CSS class
  content: ["../*.php", "../**/*.php", "./src/**/*.{js,ts,jsx,tsx,html}"],
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: {
      colors: {
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))",
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))",
        },
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
        gold: {
          DEFAULT: "hsl(var(--gold))",
          foreground: "hsl(var(--gold-foreground))",
        },
        lavender: "hsl(var(--lavender))",
        "deep-purple": "hsl(var(--deep-purple))",
        sidebar: {
          DEFAULT: "hsl(var(--sidebar-background))",
          foreground: "hsl(var(--sidebar-foreground))",
          primary: "hsl(var(--sidebar-primary))",
          "primary-foreground": "hsl(var(--sidebar-primary-foreground))",
          accent: "hsl(var(--sidebar-accent))",
          "accent-foreground": "hsl(var(--sidebar-accent-foreground))",
          border: "hsl(var(--sidebar-border))",
          ring: "hsl(var(--sidebar-ring))",
        },
      },
      borderRadius: {
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
      },
      keyframes: {
        "fly-up-1": {
          "0%": {
            transform: "translateY(100vh) rotate(0deg)",
            opacity: "0",
          },
          "10%": {
            opacity: "1",
          },
          "90%": {
            opacity: "1",
          },
          "100%": {
            transform: "translateY(-100vh) rotate(360deg)",
            opacity: "0",
          },
        },
        "fly-up-2": {
          "0%": {
            transform: "translateY(100vh) rotate(0deg) scale(1)",
            opacity: "0",
          },
          "15%": {
            opacity: "1",
          },
          "85%": {
            opacity: "1",
          },
          "100%": {
            transform: "translateY(-100vh) rotate(-360deg) scale(1.2)",
            opacity: "0",
          },
        },
        "fly-up-3": {
          "0%": {
            transform: "translateY(100vh) translate(0px) rotate(0deg)",
            opacity: "0",
          },
          "12%": {
            opacity: "1",
          },
          "88%": {
            opacity: "1",
          },
          "100%": {
            transform: "translateY(-100vh) translate(20px) rotate(180deg)",
            opacity: "0",
          },
        },

        beating: {
          "0%, 100%": { transform: "scale(1)" },
          "50%": { transform: "scale(1.08)" },
        },

        textGlowGold: {
          "0%, 100%": {
            filter: "drop-shadow(0 0 8px hsl(43 84% 51% / .6))",
          },
          "50%": {
            filter: "drop-shadow(0 0 25px hsl(43 84% 51% / .9))",
          },
        },

        marquee: {
          "0%": {
            transform: "translate(-100%)",
          },
          "100%": {
            transform: "translate(100%)",
          },
        },
      },

      animation: {
        "fly-up-1": "fly-up-1 3s linear 0s infinite normal none running",
        "fly-up-2": "fly-up-2 2.8s linear 0s infinite normal none running",
        "fly-up-3": "fly-up-3 3.8s linear 0s infinite normal none running",
        "beat-gold":
          "beating 1s ease-in-out infinite, textGlowGold 2s ease-in-out infinite",
        marquee: "marquee 30s linear infinite",
      },
    },
  },

  plugins: [require("tailwindcss-animate")],
};
