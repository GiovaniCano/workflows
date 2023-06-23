import { insertAddButton, insertDeleteButton, insertDragButton } from './_edit-buttons'

/**
 * @param {JQuery<HTMLElement>} img Image wrapper (.img)
 * @returns void
 */
export function initializeImage(img) {
    // modal Image
    img.find('img').on('click', function() {
        const modalTemplate = $($('#modal-template').html())
        modalTemplate.on('click', function(e) {
            if(e.target === e.currentTarget || e.target.classList.contains('modal-btn-close') || e.target.classList.contains('modal-content')) {
                $(this).fadeOut({complete: function() {
                    $(this).remove()
                }})
                $(document.body).removeClass('no-scroll')
            }
        })

        const imgUrl = $(this).attr('src')
        const img = $(`<img src="${imgUrl}"/>`)

        modalTemplate.find('.modal-content').append(img)
        $(document.body).append(modalTemplate)

        modalTemplate.fadeIn()
        $(document.body).addClass('no-scroll')
    })

    if($('#workflow-form').length) {
        insertDeleteButton(img)
        insertDragButton(img)
        if(!img.is(':last-child')) insertAddButton(img)
    }
}