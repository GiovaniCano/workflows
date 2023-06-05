// import './bootstrap';

$(function() {
    /* responsive sidebar */
    const sidebar = $('aside#sidebar')
    $('#sidebar-btn').on('click', function() {
        sidebar.toggleClass('show-sidebar')
        $(this).parent().toggleClass('sidebar-showing') // header
    })

    /* profile menu */
    const profileMenu = $('#profile-menu')
    const profileBtn = $('#profile-btn')
    profileBtn.on('click', function() {
        profileMenu.fadeToggle()
    })
    $(document).on('click', function(e) {
        if(!profileBtn.is(e.target) && !profileMenu.is(e.target)) profileMenu.fadeOut()
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
        const anchorsToIds = $('a[href^="#"]')
        anchorsToIds.on('click', function(e) {
            e.preventDefault()

            let currentLocation = window.location.href
            currentLocation = currentLocation.substring(0, currentLocation.indexOf('#')) + $(this).attr('href')
            history.pushState(null, null, currentLocation)

            const id = $(this).attr('href')
            smoothScrollToSection(id, 444)
        })

    /* section hover */
    $('.section-nested').on('mouseenter', function() {
        $(this).addClass('section-hover')
        $(this).parentsUntil('.section-main', '.section-nested').addClass('section-hover-disabled')
    }).on('mouseleave', function() {
        $(this).removeClass('section-hover')
        $(this).parentsUntil('.section-main', '.section-nested').removeClass('section-hover-disabled')
    })

    /* mini section*/
        //  show modal
        $('.mini-section-btn').on('click', function() {
            $('.modal#' + $(this).attr('data-modal')).fadeIn()
            $(document.body).addClass('no-scroll')
        })
        // hide modal
        $('.modal.modal-mini-section').on('click', function(e) {
            if(e.target === e.currentTarget || e.target.classList.contains('modal-btn-close') || e.target.classList.contains('modal-content')) {
                $(this).fadeOut()
                $(document.body).removeClass('no-scroll')
            }
        })

    /* zoom image */
    $('.container-images img').on('click', function() {
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
})

//////////////////////////////////////////////////////

function smoothScrollToSection(id, duration) {
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