/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./src/View/templates/**/*.php",
		"./public/**/*.html",
	],
	theme: {
		extend: {
			colors: {
				// Neo-brutalism palette — saturated blocks, harsh black, no greys.
				paper:  "#FFF8E1", // warm cream background
				ink:    "#000000", // pure black borders & shadows
				dark:   "#1A1A1A", // softer near-black for surfaces
				lime:   "#C8FF1A", // electric lime — primary accent
				pink:   "#FF80AB", // hot pink — secondary accent
				cyan:   "#5BD0F5", // sky cyan — tertiary
				coral:  "#FF6B4A", // alert / coral
				violet: "#B794F4", // soft violet
			},
			fontFamily: {
				// Archivo (geometric, ships up to wght 900) + DM Sans for body.
				display: ['"Archivo"', "system-ui", "sans-serif"],
				sans:    ['"DM Sans"', "system-ui", "sans-serif"],
				mono:    ['"DM Mono"', "ui-monospace", "monospace"],
			},
			fontSize: {
				"display-2xl": ["6rem",   { lineHeight: "0.92", letterSpacing: "-0.04em" }],
				"display-xl":  ["4.5rem", { lineHeight: "0.95", letterSpacing: "-0.035em" }],
				"display-lg":  ["3rem",   { lineHeight: "1.0",  letterSpacing: "-0.03em" }],
			},
			borderWidth: {
				3: "3px",
				5: "5px",
			},
			boxShadow: {
				// Trademark hard offset shadows — no blur, pure black blocks.
				brutal:    "6px 6px 0 0 #000000",
				"brutal-sm": "3px 3px 0 0 #000000",
				"brutal-lg": "10px 10px 0 0 #000000",
				"brutal-color": "6px 6px 0 0 #C8FF1A",
			},
			keyframes: {
				rise: {
					"0%":   { opacity: "0", transform: "translateY(20px)" },
					"100%": { opacity: "1", transform: "translateY(0)" },
				},
				wiggle: {
					"0%, 100%": { transform: "rotate(-3deg)" },
					"50%":      { transform: "rotate(3deg)" },
				},
				marquee: {
					"0%":   { transform: "translateX(0)" },
					"100%": { transform: "translateX(-50%)" },
				},
			},
			animation: {
				rise:    "rise 0.6s cubic-bezier(0.2, 0.7, 0.2, 1) both",
				wiggle:  "wiggle 2.5s ease-in-out infinite",
				marquee: "marquee 30s linear infinite",
			},
		},
	},
	plugins: [],
};
