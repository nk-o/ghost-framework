if (typeof window.ghostFrameworkWebfontList !== 'undefined') {
    const googleFonts = window.ghostFrameworkWebfontList['google-fonts'];
    if (typeof googleFonts !== 'undefined') {
        const googleFamilies = [];
        Object.keys(googleFonts).forEach((key) => {
            let weights = '';
            Object.keys(googleFonts[key].widths).forEach((keyWeight) => {
                if (keyWeight > 0 && keyWeight !== (googleFonts[key].widths.length - 1)) {
                    weights += ',';
                }
                weights += googleFonts[key].widths[keyWeight];
            });
            googleFamilies.push(`${key}:${weights}`);
        });
        window.WebFont.load({
            google: {
                families: googleFamilies,
            },
        });
    }
}
