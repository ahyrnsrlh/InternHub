import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

const withOpacity = (variableName) =>
    `rgb(var(${variableName}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Plus Jakarta Sans", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas: withOpacity("--color-canvas"),
                surface: {
                    DEFAULT: withOpacity("--color-surface"),
                    muted: withOpacity("--color-surface-muted"),
                    subtle: withOpacity("--color-surface-subtle"),
                    inverse: withOpacity("--color-surface-inverse"),
                },
                content: {
                    DEFAULT: withOpacity("--color-content"),
                    muted: withOpacity("--color-content-muted"),
                    soft: withOpacity("--color-content-soft"),
                    inverse: withOpacity("--color-content-inverse"),
                },
                line: {
                    DEFAULT: withOpacity("--color-line"),
                    soft: withOpacity("--color-line-soft"),
                    strong: withOpacity("--color-line-strong"),
                    inverse: withOpacity("--color-line-inverse"),
                },
                primary: {
                    DEFAULT: withOpacity("--color-primary"),
                    hover: withOpacity("--color-primary-hover"),
                    soft: withOpacity("--color-primary-soft"),
                    foreground: withOpacity("--color-primary-foreground"),
                },
                success: {
                    DEFAULT: withOpacity("--color-success"),
                    soft: withOpacity("--color-success-soft"),
                    foreground: withOpacity("--color-success-foreground"),
                },
                warning: {
                    DEFAULT: withOpacity("--color-warning"),
                    soft: withOpacity("--color-warning-soft"),
                    foreground: withOpacity("--color-warning-foreground"),
                },
                danger: {
                    DEFAULT: withOpacity("--color-danger"),
                    soft: withOpacity("--color-danger-soft"),
                    foreground: withOpacity("--color-danger-foreground"),
                },
            },
        },
    },

    plugins: [forms],
};
