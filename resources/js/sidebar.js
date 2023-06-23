/* responsive sidebar */
const sidebar = $('aside#sidebar')
$('#sidebar-btn').on('click', function() {
    sidebar.toggleClass('show-sidebar')
    $(this).parent().toggleClass('sidebar-showing') // header
})

/* profile menu */
const profileMenu = $('#profile-menu')
const profileBtn = $('#profile-btn')
$(document).on('click', function(e) {
    if(profileBtn.is(e.target)) {
        profileMenu.fadeIn()
    } else {
        profileMenu.fadeOut()
    }
})