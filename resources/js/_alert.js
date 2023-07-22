/**
 * @param {string} message 
 * @param {'success'|'error'} type class to be added
 */
export default function showAlert(message, type = null) {
    clearTimeout(alertTimeout)

    const alert = $('#alert')

    alert.removeClass()

    if(alert) alert.addClass('alert-'+type)
    alert.text(message)

    alert.fadeIn()

    alertTimeout = setTimeout(() => alert.fadeOut(), 3500);
}