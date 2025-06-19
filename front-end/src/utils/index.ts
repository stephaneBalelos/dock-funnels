export const slugify = (text: string): string => {
    // Convert the input text to a slug format
    // Slug format: lowercase, spaces replaced with unsderscores, non-word characters removed, and trimmed, special characters like hyphens and umlauts handled
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '_') // Replace spaces with underscores
        .replace(/--+/g, '_') // Replace multiple hyphens
        .replace(/^-+/, '') // Trim hyphens from the start
        .replace(/-+$/, '') // Trim hyphens from the end
        .replace(/ä/g, 'ae') // Replace umlaut 'ä' with 'ae'
        .replace(/ö/g, 'oe') // Replace umlaut 'ö' with 'oe'
        .replace(/ü/g, 'ue') // Replace umlaut 'ü' with 'ue'
        .replace(/ß/g, 'ss') // Replace sharp s 'ß' with 'ss'
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters except hyphens
        .replace(/[^a-z0-9\-]/g, '') // Remove any remaining non-alphanumeric characters except hyphens
        .trim(); // Trim whitespace

}