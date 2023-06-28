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
        const btnAdd = $(e.target)
        if(btnAdd.parent().parent().is('#workflow-form')) {
            btnAdd.next('.btn-add-menu').find('.js-btnadd-section').trigger('click')
            return
        }

        $('.btn-add-menu:visible').fadeOut()
        btnAdd.next('.btn-add-menu').fadeIn()
    } else {
        $('.btn-add-menu:visible').fadeOut()
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
    // on anchor click
    const anchorsToIds = $('a.sidebar-section')
    anchorsToIds.each(function() {
        $(this).on('click', function(e) {
            e.preventDefault()
            addSmoothScrollToSidebarItem($(this))
        })
    })
