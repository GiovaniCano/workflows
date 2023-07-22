/**
 * Make a slug
 * @param {string} input Value to convert
 * @returns {string}
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
    const randomString = Math.random().toString(36).substring(2, 7)
    return timestamp + randomString
}

/**
 * Log the form data to the console
 * @param {FormData} formData
 * @returns {void}
 */
export function logFormData(formData) {
    formData.forEach((value, key) => {
        console.log(`${key}:`, value);
    });
}

/**
 * Use they given key to get the translated text from the translation object
 * @param {string} key 
 * @returns {string}
 * @example __('errors.message')
 * @example __('key')
 */
export function __(key) {
    const keys = key.split('.')
    try {
        let trans

        keys.forEach((key, index) => {
            if(index === 0) {
                trans = translations[key]
            } else {
                trans = trans[key]
            }
        })
        return trans

    } catch (error) {
        console.error(`${key} does not exists in translations`);
        return keys[keys.length - 1]
    }
}