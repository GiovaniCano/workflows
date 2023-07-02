import { insertAddButton } from './_edit-buttons'
import { initializeMiniSectionButton, initializeSection, smoothScrollToSection, addSmoothScrollToSidebarItem } from './_sections';
import { initializeWysiwyg } from './_wysiwygs';
import { initializeImage } from './_images';

import './_form'

/* 
    The "prevent to leave page" functionality is being pushed from views/workflows/form.blade.php
*/

/* toggle add menu */
$(document).on('click', function(e) {
    if($(e.target).next().is('.btn-add-menu:hidden')) {
        /**
         * @type JQuery<HTMLElement>
         */
        const btnAdd = $(e.target)

        // no menu, only sections allowed
        if(btnAdd.parent().parent().is('#workflow-form')) {
            btnAdd.next('.btn-add-menu').find('.js-btnadd-section').trigger('click')
            return
        }

        // hide visible menus (including the current one) and reset styles
        $('.btn-add-menu:visible').fadeOut({complete: function() { $(this).removeAttr('style') }})

        // show menu
        const menu = btnAdd.next('.btn-add-menu')
        menu.fadeIn()

        // adjust menu so it doesn't be outside the screen
        const margin = 5
        const scrollbarWidth = 10
        
        const rect = menu.get(0).getBoundingClientRect()
        const left = rect.left
        const right = rect.left + menu.get(0).offsetWidth
        const windowWidth = window.innerWidth

        if(left < margin) {
            const x = Math.abs(left) + margin
            menu.css('left', `calc(50% + ${x}px)`)
        } else if(right > windowWidth - margin) {
            const x = (right - windowWidth) + (margin + scrollbarWidth)
            menu.css('left', `calc(50% - ${x}px)`)
        }
    } else {
        // hide visible menus (including the current one) and reset styles
        $('.btn-add-menu:visible').fadeOut({complete: function() { $(this).removeAttr('style') }})
    }
})

/* generate initial buttons */
    /* sections */
    $('#workflow-form .section-form').each(function() {
        initializeSection($(this))
    })

    /* mini sections */
    $('.mini-section-btn-wrapper').each(function() {
        initializeMiniSectionButton($(this))
    })

    /* wysiwygs */
    $('#workflow-form .wysiwyg-content').each(function(i) {
        setTimeout(() => {
            initializeWysiwyg($(this))
        }, i * 300);
    })

    /* images */
    $('.container-images .img').each(function() {
        initializeImage($(this))
    })

    /* other add buttons */
    $('#workflow-form').find('.container-form, .workflow-header').each(function() {
        insertAddButton($(this))
    })

/* smooth scroll to sections */
    // on load
    const url = window.location.href
    const urlIdIndex = url.indexOf('#')
    if(urlIdIndex !== -1) {
        const urlId = url.substring(urlIdIndex)
        smoothScrollToSection(urlId, 777)
    }
    // sidebar links
    const anchorsToIds = $('a.sidebar-section')
    anchorsToIds.on('click', function(e) {
        e.preventDefault()
        addSmoothScrollToSidebarItem($(this))
    })
    // sections links
    $('section h2 a').on('click', function(e) {
        e.preventDefault()
        const id = $(this).attr('href')
        smoothScrollToSection(id ,444)
    })

// sidebar sections highlights
window.addEventListener('load', function() { // to wait until all the content is loaded in the form
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('.js-sidebar-highlight-container')
        let highlights = $()
    
        sections.forEach(section => {
            const rect = section.getBoundingClientRect()
            if (rect.top >= 0 && rect.top <= 50) {
                $(`#sidebar-list-sections a`).removeClass('current')
    
                const id = section.querySelector('.js-sidebar-highlight-target').id
                highlights = highlights.add($(`#sidebar-list-sections a[href="#${id}"]`))
            }
        })
    
        highlights.addClass('current')
    })
})