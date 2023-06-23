/* submit workflow form */
$('#workflow-form').on('submit', function(e) {
    e.preventDefault()
    
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log(csrfToken)
    
    window.onbeforeunload = null
    window.location.href = window.location.href.replace('/edit/', '/') // don't use this, use the url provided by the json response instead, when available
})

/* no submit workflow with enter */
$('#workflow-form').on('keydown', function(e) {
    if(e.code === 'Enter') e.preventDefault()
})