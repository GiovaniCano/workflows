import alert from '../_alert'

/* submit workflow form */
$('#workflow-form').on('submit', function(e) {
    e.preventDefault()
    if(!wysiwygsAreValid()) return alert(translations.textsMustBeValid, 'error')
    
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log(csrfToken)
    
    window.onbeforeunload = null
    window.location.href = window.location.href.replace('/edit/', '/') // don't use this, use the url provided by the json response instead, when available
})

/* no submit workflow with enter */
$('#workflow-form').on('keydown', function(e) {
    if(e.code === 'Enter' && !e.target.classList.contains('ck-editor__editable')) e.preventDefault()
})

/**
 * @returns boolean
 */
function wysiwygsAreValid() {
    // var editors in views/workflows/form
    const editorsValid = Object.values(editors).map(editor => editor.valid)
    for(let valid of editorsValid) {
        if(!valid) return false
    }
    return true
}