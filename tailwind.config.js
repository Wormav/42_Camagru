/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./src/View/templates/**/*.php", "./public/**/*.html", "./public/**/*.js"],
	theme: {
		extend: {
			colors: {
				paper: "#FFF8E1",
				ink: "#000000",
				dark: "#1A1A1A",
				lime: "#C8FF1A",
				pink: "#FF80AB",
				cyan: "#5BD0F5",
				coral: "#FF6B4A",
				red: "#DC2626",
				violet: "#B794F4",
			},
			fontFamily: {
				display: ['"Archivo"', "system-ui", "sans-serif"],
				sans: ['"DM Sans"', "system-ui", "sans-serif"],
				mono: ['"DM Mono"', "ui-monospace", "monospace"],
			},
			fontSize: {
				"display-2xl": ["6rem", { lineHeight: "0.92", letterSpacing: "-0.04em" }],
				"display-xl": ["4.5rem", { lineHeight: "0.95", letterSpacing: "-0.035em" }],
				"display-lg": ["3rem", { lineHeight: "1.0", letterSpacing: "-0.03em" }],
			},
			borderWidth: {
				3: "3px",
				5: "5px",
			},
			boxShadow: {
				brutal: "6px 6px 0 0 #000000",
				"brutal-sm": "3px 3px 0 0 #000000",
				"brutal-lg": "10px 10px 0 0 #000000",
				"brutal-color": "6px 6px 0 0 #C8FF1A",
			},
			keyframes: {
				rise: {
					"0%": { opacity: "0", transform: "translateY(20px)" },
					"100%": { opacity: "1", transform: "translateY(0)" },
				},
				wiggle: {
					"0%, 100%": { transform: "rotate(-3deg)" },
					"50%": { transform: "rotate(3deg)" },
				},
				marquee: {
					"0%": { transform: "translateX(0)" },
					"100%": { transform: "translateX(-50%)" },
				},
			},
			animation: {
				rise: "rise 0.6s cubic-bezier(0.2, 0.7, 0.2, 1) both",
				wiggle: "wiggle 2.5s ease-in-out infinite",
				marquee: "marquee 30s linear infinite",
			},
		},
	},
	plugins: [],
};
