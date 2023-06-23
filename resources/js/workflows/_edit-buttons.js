import { generateUniqueId } from "../_helpers"
import { initializeImage } from "./_images"
import { initializeSection, createSidebarSectionItem, initializeMiniSectionButton, addSectionToSidebar } from "./_sections"
import { initializeWysiwyg } from "./_wysiwygs"

/**
 * Create and append the delete button in the given element
 * @param {JQuery<HTMLElement>} element 
 * @returns {void}
 */
export function insertDeleteButton(element) {
    const btn = $(`
        <button type="button" class="btn-delete">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" stroke-width="2.5" stroke="#013c6499" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /><path d="M10 12l4 4m0 -4l-4 4" />
            </svg>
        </button>
    `)
    btn.on('click', function() {
        let target = $(this).closest('.js-action-target')

        // mini section btn
        if(target.hasClass('mini-section-btn-wrapper')) {
            const miniSectionId = target.find('.mini-section-btn').attr('data-minisection-btn-id')
            const modal = $(`[data-minisection-modal-id="${miniSectionId}"]`)
            modal.find('.modal-btn-close').trigger('click')
            modal.remove()
        // mini section modal
        } else if(target.hasClass('modal-mini-section')) {
            const miniSectionId = target.attr('data-minisection-modal-id')
            const button = $(`[data-minisection-btn-id="${miniSectionId}"]`)
            button.next('.btn-delete').trigger('click')
            return
        }
        
        // if section: delete it from the sidebar
        if(target.is('.section-form') || $(this).prev().is('.mini-section-btn')) {
            const sectionId = target.attr('data-id') ?? $(this).prev().attr('data-id')
            const sidebarItem = $(`.sidebar-section[data-id="${sectionId}"]`).parent('li')
            // delete nested sections
            target.find('.section-nested, .mini-section-btn-wrapper').each(function() {
                $(this).find('.btn-delete').trigger('click')
            })
            sidebarItem.remove()
        }

        // containers
        if(target.parent().is('.container-form')) {
            // remove container if last child
            if(target.siblings().length == 0) {
                const container = target.parent()
                container.after(target)
                container.remove()
            }
            // last in container
            if(target.is(':last-child')) target.prev('.btn-add-wrapper').remove()
        }

        // merge containers
        if(
            target.prev().prev().is('.container-images') && target.next().next().is('.container-images') || 
            target.prev().prev().is('.container-sections') && target.next().next().is('.container-sections')
        ) {
            const container1 = target.prev().prev('.container-form')
            const container2 = target.next().next('.container-form')
            insertAddButton(container1.children().last())
            container1.append(container2.children())
            container2.next('.btn-add-wrapper').remove()
            container2.remove()
        }

        target.next('.btn-add-wrapper').remove()
        target.remove()
    })

    element.append(btn)
}

/**
 * Create and append the drag button in the given element
 * @param {JQuery<HTMLElement>} element 
 * @returns {void}
 */
export function insertDragButton(element) {
    const btn = $(`
        <button type="button" class="btn-drag" tabindex="-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="#965cc2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M5 15m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 15m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M19 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M19 15m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
            </svg>
        </button>
    `)
    btn.on('click', function() {
        const target = $(this).closest('.js-action-target')
        console.log(target)
    })

    element.prepend(btn)
}

/**
 * Create and append the add button in the given element
 * @param {JQuery<HTMLElement>} element 
 * @returns {void}
 */
export function insertAddButton(element) {
    if(element.hasClass('section-mini')) return

    const insideMniniSection = Boolean(element.closest('.section-mini').length)

    const addButtonHtml = ''.concat(
        `
            <div class="btn-add-wrapper">
                <button type="button" class="btn-add">+</button>
                <ul class="unstyled-list btn-add-menu" style="display:none">`
                ,
                insideMniniSection ? '' : `
                    <li><button type="button" class="js-btnadd-section">Section</button></li>
                    <li><button type="button" class="js-btnadd-minisection">Mini Section</button></li>`
                ,
                    `<li><button type="button" class="js-btnadd-wysiwyg">Text</button></li>
                    <li>
                        <button type="button" class="js-btnadd-image">Image</button>
                        <input type="file" style="display: none;">
                    </li>
                </ul>
            </div>
        `
    )
    const addButton = $(addButtonHtml)
    addButton.find('input[type="file"]').on('change', function() {
        addAndPreviewImage($(this), addButton)
    })

    // show "add menu" functionality is in ./index.js

    /* add section */
    addButton.find('.js-btnadd-section').on('click', function() {
        const sectionTemplate = $($('#section-template').html())
        const input = sectionTemplate.find('header input')

        const sectionClass = addButton.parent().is('#workflow-form') ? 'section-main' : 'section-nested'
        const id = generateUniqueId()

        sectionTemplate.addClass(sectionClass)
        sectionTemplate.attr('data-id', id)
        input.attr('data-id', id)
        input.attr('id', id + '-')

        if(addButton.parent().is('.container-form')) {
            splitContainer(addButton)
        }

        addButton.after(sectionTemplate)

        // sidebar item
        if(sectionClass === 'section-main') {
            const sidebarSectionItem = createSidebarSectionItem(id, true)

            const previousSection = sectionTemplate.prevAll('.section-main').first()
            if(previousSection.length == 0) { // is the first one
                $('#sidebar-list-sections > ol').prepend(sidebarSectionItem)
            } else {
                const previousId = previousSection.attr('data-id')
                const previousSidebarSection = $(`.sidebar-section[data-id="${previousId}"]`).parent('li')
                previousSidebarSection.after(sidebarSectionItem)
            }
        } else {
            addSectionToSidebar(id, sectionTemplate)
        }

        initializeSection(sectionTemplate)
    })
    
    /* add minisection */
    addButton.find('.js-btnadd-minisection').on('click', function() {
        const minisectionbuttonWrapperTemplate = $($('#minisection-btn-template').html())
        const modalTemplate = $($('#minisection-modal-template').html())

        const button = minisectionbuttonWrapperTemplate.find('.mini-section-btn')
        const miniSection = modalTemplate.find('.section-mini')
        const input = miniSection.find('header input')

        const id = generateUniqueId()

        button.attr('id', id + '-')
        button.attr('data-id', id)
        button.attr('data-minisection-btn-id', id)
        
        modalTemplate.attr('data-minisection-modal-id', id)
        miniSection.attr('data-id', id)
        input.attr('data-id', id)
        input.attr('id', id + '-')

        if(addButton.parent().is('.container-images')) {
            splitContainer(addButton)
        }
        
        let container = addButton.parent('.container-sections')
        if(container.length == 0) {
            if(addButton.next().is('.container-sections')) {
                addButton.next().prepend(minisectionbuttonWrapperTemplate)
            } else if (addButton.prev().is('.container-sections')) {
                insertAddButton(addButton.prev().children().last())
                addButton.prev().append(minisectionbuttonWrapperTemplate)
            } else {
                container = $(`<section class="container-sections container-form m-b-1"></section>`)
                container.append(minisectionbuttonWrapperTemplate)
                addButton.after(container)
                insertAddButton(container)
            }
        } else {
            addButton.after(minisectionbuttonWrapperTemplate)
        }

        $('#workflow-form').append(modalTemplate)

        addSectionToSidebar(id, button)
        
        initializeMiniSectionButton(minisectionbuttonWrapperTemplate)
        initializeSection(miniSection)
    })

    
    /* add wysiwyg */
    addButton.find('.js-btnadd-wysiwyg').on('click', function() {
        const wysiwygTemplate = $($('#wysiwyg-template').html())
        if(addButton.parent().is('.container-form')) {
            splitContainer(addButton)
        }
        addButton.after(wysiwygTemplate)
        initializeWysiwyg(wysiwygTemplate)
    })
    

    /* add image */
    addButton.find('.js-btnadd-image').on('click', function() {
        $(this).next('input[type="file"]').trigger('click')
        // add image functionality is being handled by the 'onChange' event of the 'input[type="file"]' (next to the "add button" template at the top of this file)
    })
    
    element.after(addButton)
} // end insertAddButton()

/**
 * Split the sections or image containers into two parts starting from the addButton, while maintaining the addButton between the generated containers.
 * @param {JQuery<HTMLElement>} addButton
 * @returns void
 */
function splitContainer(addButton) {
    const originalContainer = addButton.parent('.container-form')
    const containerElements = originalContainer.children()

    const buttonIndex = addButton.index()

    const firstGroup = containerElements.slice(0, buttonIndex)
    const secondGroup = containerElements.slice(buttonIndex + 1)

    const newContainer1 = originalContainer.clone().empty()
    newContainer1.append(firstGroup)
    const newContainer2 = originalContainer.clone().empty()
    newContainer2.append(secondGroup)

    originalContainer.after(newContainer2)
    originalContainer.after(addButton)
    originalContainer.after(newContainer1)
    originalContainer.remove()
}

/**
 * Add the image selected by the user after the used add button and show a preview
 * @param {JQuery<HTMLElement>} targetInput Input where the file is going to be linked
 * @param {JQuery<HTMLElement>} addButton "Add button" that owns the input
 * @returns void
 */
function addAndPreviewImage(targetInput, addButton) {
    const file = targetInput.get(0).files[0]
    if(!file) return

    const newInput = $('<input type="file" style="display: none;">')
    newInput.on('change', function() {
        addAndPreviewImage($(this), addButton)
    })
    targetInput.before(newInput)
    
    const imgTemplate = $($('#image-template').html())
    imgTemplate.append(targetInput)

    const reader = new FileReader()
    reader.onload = e => imgTemplate.find('img').attr('src', e.target.result)
    reader.readAsDataURL(file)

    if(addButton.parent().is('.container-sections')) {
        splitContainer(addButton)
    }

    let container = addButton.parent('.container-images')
    if(container.length == 0) {
        if(addButton.next().is('.container-images')) {
            addButton.next().prepend(imgTemplate)
        } else if (addButton.prev().is('.container-images')) {
            insertAddButton(addButton.prev().children().last())
            addButton.prev().append(imgTemplate)
        } else {
            container = $(`<section class="container-images container-form m-b-1"></section>`)
            container.append(imgTemplate)
            addButton.after(container)
            insertAddButton(container)
        }
    } else {
        addButton.after(imgTemplate)
    }

    initializeImage(imgTemplate)
}