import { insertAddButton, insertDeleteButton, insertDragButton } from './_edit-buttons'
import { createSlug } from '../_helpers'
import { draggableOptionsBase } from '../_jquery_ui'

/**
 * @param {JQuery<HTMLElement>} section
 * @returns void
 */
export function initializeSection(section) {
    insertDeleteButton(section.find('.section-header').first())
    if(section.hasClass('section-nested') || section.hasClass('section-main')) {
        insertDragButton(section.find('.section-header').first())
    }
    insertAddButton(section.find('.section-top-button-hook').first()) // at the beginning of the section
    insertAddButton(section) // after the section

    if(!section.is('.section-mini')) section.draggable(draggableOptionsBase)

    section.find('.form-control').on('keyup', function() {
        const control = $(this)
    
        const seccId = control.attr('data-id')
        const val = control.val()
        const htmlId = seccId + '-' + createSlug(val)
    
        const sidebarEl = $('#sidebar-section-link-'+seccId)
        const sidebarElMaxLen = sidebarEl.attr('data-maxlen')
    
        const valSub = val.length > sidebarElMaxLen ? val.substring(0, sidebarElMaxLen)+'...' : val
    
        sidebarEl.text('# '+valSub)
        sidebarEl.attr('href', '#'+htmlId)
        sidebarEl.attr('title', val)
        
        if(control.attr('data-mini')) {
            const btn = $(`[data-minisection-btn-id="${seccId}"]`)
            btn.attr('id', htmlId)
            btn.text(val)
        } else {
            control.attr('id', htmlId)
        }
    })
}

/**
 * @param {JQuery<HTMLElement>} buttonWrapper 
 * @returns void
 */
export function initializeMiniSectionButton(buttonWrapper) {
    const button = buttonWrapper.find('.mini-section-btn')
    const id = button.attr('data-minisection-btn-id')
    const modal = $(`[data-minisection-modal-id="${id}"]`)

    button.on('click', function() {
        modal.fadeIn()
        $(document.body).addClass('no-scroll')
    })
    modal.on('click', function(e) {
        if(e.target === e.currentTarget || e.target.classList.contains('modal-btn-close') || e.target.classList.contains('modal-content')) {
            $(this).fadeOut()
            $(document.body).removeClass('no-scroll')
        }
    })

    if($('#workflow-form').length) {
        insertDeleteButton(buttonWrapper)
        insertDragButton(buttonWrapper)
        if(!buttonWrapper.is(':last-child')) insertAddButton(buttonWrapper)

        buttonWrapper.draggable(draggableOptionsBase)
    }
}

/**
 * Create a sidebar list element (<li><a></a></li>)
 * @param {string} id Unique id to insert
 * @param {boolean} isMain Determine if the section is a man section
 * @returns {JQuery<HTMLElement>} A sidebar list element
 */
export function createSidebarSectionItem(id, isMain) {
    const sidebarElTemplate = $($('#sidebar-el-template').html())
    sidebarElTemplate.attr('href', '#' + id + '-')
    sidebarElTemplate.attr('data-id', id)
    sidebarElTemplate.attr('id', sidebarElTemplate.attr('id')+id)
    sidebarElTemplate.attr('data-maxlen', isMain ? 18 : 15 )
    sidebarElTemplate.on('click', function(e) {
        e.preventDefault()
        addSmoothScrollToSidebarItem($(this))
    })

    let li
    if(isMain) {
        li = $('<li><ol class="unstyled-list"></ol></li>')
    }else {
        li = $('<li>')
    }
    li.prepend(sidebarElTemplate)

    return li
}

/**
 * Add smooth scroll functionality to a sidebar anchor
 * @param {JQuery<HTMLElement>} sidebarItemAnchor Anchor inside a li in the sidebar
 * @returns void
 */
export function addSmoothScrollToSidebarItem(sidebarItemAnchor) {
    const currentLocation = window.location.href
    const newLocation = currentLocation.substring(0, currentLocation.indexOf('#')) + sidebarItemAnchor.attr('href')
    const action = currentLocation.split('/')[4]
    if(action !== 'edit' && action !== 'create') {
        history.pushState(null, null, newLocation)
    }

    const id = sidebarItemAnchor.attr('href')
    smoothScrollToSection(id, 444)
}

/**
 * Scroll to the given section id
 * @param {string|number} id Destination
 * @param {number} duration 
 * @returns void
 */
export function smoothScrollToSection(id, duration) {
    const target = $(id)
    if(!target.length) return 

    // highlight current section in sidebar
    $(`#sidebar-list-sections a`).removeClass('current')
    $(`#sidebar-list-sections a[href="${id}"]`).addClass('current')

    const currentScrollPos = window.scrollY
    const targetPos = target.offset().top

    const responsiveBeakpointsLg = 992
    const margin = 18
    let headerHeight = 50
    if(window.innerWidth >= responsiveBeakpointsLg) headerHeight = 0

    if (targetPos <= currentScrollPos + (margin + headerHeight) && targetPos >= currentScrollPos - (margin + headerHeight)) return

    let direction
    if(targetPos > currentScrollPos) { //down
        direction = 1
    } else if (targetPos < currentScrollPos) { //up
        direction = -1
    }

    const scrollTo = target.offset().top - margin - headerHeight
    $('html').animate({
        scrollTop: scrollTo + (margin+10)*direction, // bounce
    }, duration)
    .animate({
        scrollTop: scrollTo, // target position
    }, 100)
}

/**
 * @param {string} sectionClass 
 * @param {JQuery<HTMLElement>} sectionOrbutton If it's a button, don't use the not wrapper, use the button itself
 * @param {string} id 
 * @param {JQuery<HTMLElement>|null} sidebarSectionItem Item to add to the sidebar or null to create a new one
 * @returns void
 */
export function addSectionToSidebar(sectionClass, sectionOrbutton, id, sidebarSectionItem = null) {
    if(sectionClass === 'section-main') {
        if(!sidebarSectionItem) sidebarSectionItem = createSidebarSectionItem(id, true)

        const previousSection = sectionOrbutton.prevAll('.section-main').first()
        if(previousSection.length == 0) { // is the first one
            $('#sidebar-list-sections > ol').prepend(sidebarSectionItem)
        } else {
            const previousId = previousSection.attr('data-id')
            const previousSidebarSection = $(`.sidebar-section[data-id="${previousId}"]`).parent('li')
            previousSidebarSection.after(sidebarSectionItem)
        }
    } else {
        if(!sidebarSectionItem) sidebarSectionItem = createSidebarSectionItem(id, false)
    
        const main = sectionOrbutton.parents('.section-main')
        const sections = main.find('.section-nested, .mini-section-btn')
        
        const sectionIndex = sections.index(sectionOrbutton)
        if(sectionIndex == 0) { // is the first one
            const mainId = main.attr('data-id')
            const mainSidebarItem = $(`.sidebar-section[data-id="${mainId}"]`)
            mainSidebarItem.next('ol').prepend(sidebarSectionItem)
        } else {
            const previousSection = sections.eq(sectionIndex - 1)
            const previousId = previousSection.attr('data-id')
            const previousSidebarItem = $(`.sidebar-section[data-id="${previousId}"]`).parent('li')
            previousSidebarItem.after(sidebarSectionItem)
        }
    }
}