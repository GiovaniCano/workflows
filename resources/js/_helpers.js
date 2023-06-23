/**
 * Make a slug
 * @param {string} input Value to convert
 * @returns string
 */
export function createSlug(input) {
    return input
        .replace(/[^\w\s]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .toLowerCase()
}

export function generateUniqueId() {
    const timestamp = Date.now().toString(36)
    const randomString = Math.random().toString(36).substring(2,7)
    return timestamp + randomString
}