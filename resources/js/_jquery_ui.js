import { insertAddButton, insertImageOrMinisectionButton, insertSectionOrWysiwyg, mergeSurroundingContainers } from "./workflows/_edit-buttons"
import { addSectionToSidebar } from "./workflows/_sections"

/**
 * @type JQueryUI.DraggableOptions
 */
const draggableOptions = {
    revert: true,
    handle: '.btn-drag:first',
    zIndex: 9999,
    scroll: false,
    drag: function(e, ui) {
        const scrollSensitivity = 44
        const scrollRate = 20

        const modal = $('.modal:visible .modal-content').get(0)
        if(modal) {
            // the css method used to make the modals scrollable while keeping the content vertically centered makes the .modal and the .modal-content to not get the "real height", so it has to be the section+padding instead
            const modalStyles = getComputedStyle(modal)
            const modalPaddingY = parseInt(modalStyles.paddingTop) + parseInt(modalStyles.paddingBottom)

            const windowHeight = window.innerHeight
            const modalHeight = modal.querySelector('section').clientHeight + modalPaddingY
            const mouseY = e.clientY
            const scrollYBottom = modal.scrollTop + windowHeight
    
            if(mouseY < 0 + scrollSensitivity) modal.scrollBy(0, -scrollRate)
            if((mouseY > windowHeight - scrollSensitivity) && !(scrollYBottom >= modalHeight)) modal.scrollBy(0, scrollRate)
        } else {
            const windowHeight = window.innerHeight
            const documentHeight = document.body.clientHeight
            const mouseY = e.clientY
            const scrollYBottom = window.scrollY + windowHeight
    
            if(mouseY < 0 + scrollSensitivity) window.scrollBy(0, -scrollRate)
            if((mouseY > windowHeight - scrollSensitivity) && !(scrollYBottom >= documentHeight)) window.scrollBy(0, scrollRate)
        }
    },
    start: function(e, ui) {
        const item = ui.helper
        const previousAddBtn = item.prev('.btn-add-wrapper').length ? item.prev('.btn-add-wrapper') : item.parent('.container-form').prev('.btn-add-wrapper')
        const nextAddBtn = item.next('.btn-add-wrapper').length ? item.next('.btn-add-wrapper') : item.parent('.container-form').next('.btn-add-wrapper')

        let mainSectionAddButtons = $()
        if(!item.hasClass('section-form')) mainSectionAddButtons = mainSectionAddButtons.add('#workflow-form > .btn-add-wrapper')

        previousAddBtn.add(nextAddBtn).add(mainSectionAddButtons).children('.droppable').addClass('droppable-invalid').prop('disabled', true)
    },
    stop: function() {
        $('.droppable-invalid').prop('disabled', false).removeClass('droppable-invalid')
    },
}
export const draggableOptionsBase = Object.freeze(draggableOptions)

/**
 * @type JQueryUI.DroppableOptions
 */
const droppableOptions = {
    tolerance: 'pointer',
    drop: function(event, ui) {
        const btn = $(this).parent()
        const draggedItem = ui.draggable

        if( // check if dragged item is after or before the selected droppable
            ( !btn.next().is(draggedItem) && !btn.prev().is(draggedItem) ) && 
            // container after
            ( !btn.next('.container-form').children().first().is(draggedItem) && !btn.prev().is(draggedItem) ) &&
            // container before
            ( !btn.prev('.container-form').children().last().is(draggedItem) && !btn.next().is(draggedItem) )
        ) {
            // item was in a container
            if(draggedItem.parent().is('.container-form')) {
                // remove container if last child
                if(draggedItem.siblings().length == 0) {
                    const container = draggedItem.parent()
                    container.after(draggedItem)
                    container.remove()
                }
                // last in container
                if(draggedItem.is(':last-child')) draggedItem.prev('.btn-add-wrapper').remove()
            }

            // item was surrounded by containers of the same type
            mergeSurroundingContainers(draggedItem)

            // remove next add button before move the dragged item
            draggedItem.next('.btn-add-wrapper').remove()

            //---- move the dragged item ----
            if(draggedItem.is('.section-form') || draggedItem.is('.wysiwyg-content')) {
                insertSectionOrWysiwyg(btn, draggedItem)
            } else {
                insertImageOrMinisectionButton(btn, draggedItem)
            }

            // add the addbutton
            if(!draggedItem.parent().is('.container-form') || (draggedItem.parent().is('.container-form') && !draggedItem.is(':last-child'))) {
                insertAddButton(draggedItem)
            }

            // change section type and update the sidebar
            if(draggedItem.is('.section-form')) {
                if(draggedItem.hasClass('section-main') && !draggedItem.parent().is('#workflow-form')) {
                    draggedItem.removeClass('section-main').addClass('section-nested')
                } else if(draggedItem.hasClass('section-nested') && draggedItem.parent().is('#workflow-form')) {
                    draggedItem.removeClass('section-nested').addClass('section-main')
                }

                let sectionClass = draggedItem.is('.section-main') ? 'section-main' : 'section-nested'
                const id = draggedItem.attr('data-id')

                let sidebarEl = $(`.sidebar-section[data-id="${id}"]`).parent('li')

                addSectionToSidebar(sectionClass, draggedItem, id, sidebarEl)

                if(sectionClass == 'section-main') {
                    const ol = $('<ol class="unstyled-list"></ol>')
                    draggedItem.find('.section-nested, .mini-section-btn-wrapper').each(function() {
                        const id = $(this).attr('data-id') || $(this).find('.mini-section-btn').attr('data-id')
                        ol.append($(`.sidebar-section[data-id="${id}"]`).parent('li'))
                    })
                    sidebarEl.append(ol)
                } else { // nested
                    const ol = sidebarEl.find('ol')
                    sidebarEl.after(ol.children())
                    ol.remove()
                }

            } else if(draggedItem.is('.mini-section-btn-wrapper')) {
                const minisectionButton = draggedItem.find('.mini-section-btn')
                const id = minisectionButton.attr('data-id')

                const sidebarEl = $(`.sidebar-section[data-id="${id}"]`).parent('li')

                addSectionToSidebar('', minisectionButton, id, sidebarEl)
            }
        }
    },
}
export const droppableOptionsBase = Object.freeze(droppableOptions)