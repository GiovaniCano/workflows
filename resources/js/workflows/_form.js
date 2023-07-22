import showAlert from '../_alert'
import { __, logFormData } from '../_helpers'

/* submit workflow form */
$('#workflow-form').on('submit', function(e) {
    e.preventDefault()
    const form = $(this)

    // validate wysiwygs max length
    const wysiwygs = Object.entries(editors) // var editors in views/workflows/form
    for(const [id, { valid }] of wysiwygs) {
        if(!valid) {
            const wysiwyg = $(`.wysiwyg-content[data-editor-id="${id}"]`)
            const modal = wysiwyg.closest('.modal')

            if(modal.length > 0) {
                const modalId = modal.attr('data-minisection-modal-id')
                const miniSectionButton = $(`.mini-section-btn[data-minisection-btn-id="${modalId}"]`)
                $('html').scrollTop(miniSectionButton.offset().top - 18)
                miniSectionButton.trigger('click')
                const modalContent = modal.find('.modal-content')
                modalContent.scrollTop(0)
            } else {
                $('html').scrollTop(wysiwyg.offset().top - 18)
            }

            showAlert(__('submitErrors.invalidWysiwyg'), 'error')
            return
        }
    }

    // validate required input (max length is validated by default with maxlength="25" attribute)
    const closestInvalidInput = form.find('input[type="text"]:invalid').first()
    if(closestInvalidInput.length > 0) {
        if(closestInvalidInput.attr('name') === 'workflow_name') {
            $('html').scrollTop(closestInvalidInput.offset().top - 27)
            showAlert(__('submitErrors.requiredWorkflowName'), 'error')
            return
        }   

        const modal = closestInvalidInput.closest('.modal')
        if(modal.length > 0) {
            const modalId = modal.attr('data-minisection-modal-id')
            const miniSectionButton = $(`.mini-section-btn[data-minisection-btn-id="${modalId}"]`)
            $('html').scrollTop(miniSectionButton.offset().top - 18)
            miniSectionButton.trigger('click')
        } else {
            $('html').scrollTop(closestInvalidInput.offset().top - 18)
        }
        showAlert(__('submitErrors.requiredSectionName'), 'error')
        return
    }
    
    // submit feedback
    showAlert(__('submiting'))
    const submitInputs = form.find('[type="submit"]')
    submitInputs.attr('disabled', true).addClass('loading-cursor')

    const formData = new FormData()
    
    formData.append('name', form.find('[name="workflow_name"]').val())

    // sections
    const mainSections = form.find('.section-main')
    mainSections.each(function(index) {
        formData.append('main_sections[]', JSON.stringify( makeSectionObject($(this), index) ))
    })

    // uploaded images (var images in form.blade.php)
    Object.entries(images).forEach(([key, file]) => {
        formData.append(`images[${key}]`, file)
    })

    // deleted records (var deletedRecords in form.blade.php)
    formData.append('deleted[sections]', deletedRecords['sections'])
    formData.append('deleted[wysiwygs]', deletedRecords['wysiwygs'])
    formData.append('deleted[images]', deletedRecords['images'])

    // other data
    const csrfToken = $('meta[name="csrf-token"]').attr('content')
    formData.append('_method', form.attr('method'))

    // logFormData(formData)

    // request
    ;(async function() {
        try {
            const response = await fetch(form.attr('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            
            const data = await response.json()

            if(!response.ok) throw {
                code: response.status,
                message: response.statusText,
                data: data
            }
          
            // console.log(data);

            window.onbeforeunload = null
            window.location.href = data.workflow_url
        } catch (error) {
            console.error(error)

            let message

            if(error?.data?.errors?.name) message = error.data.errors.name

            showAlert(message || __('submitErrors.generic'), 'error')
            submitInputs.attr('disabled', false).removeClass('loading-cursor')
        }
    })()
})

/* no submit workflow with enter */
$('#workflow-form').on('keydown', function(e) {
    if(e.code === 'Enter' && !e.target.classList.contains('ck-editor__editable')) e.preventDefault()
})

//////////////////////////////////////////////////////

/**
 * Make an object representation of the section and its content
 * @param {JQuery<HTMLElement>} sectionHtml 
 * @param {number} position
 * @returns {{}} Object representation of the section
 */
function makeSectionObject(sectionHtml, position) {
    const sectionObj = {
        name: sectionHtml.find('input').first().val(),
        id: sectionHtml.attr('data-id'),
        position: position
    }

    const contentHtml = sectionHtml.find('> div').find('> .wysiwyg-content, > .container-form > *:not(.btn-add-wrapper), > .section-nested')
    // console.log(contentHtml);
    if(contentHtml.length) {
        sectionObj.content = {}

        contentHtml.each(function(index) {
            const element = $(this)

            let key, value
            
            if(element.is('.section-nested')) { 
                // section
                key = 'sections'
                value = makeSectionObject(element, index)
            } else if(element.is('.mini-section-btn-wrapper')) { 
                // minisection
                const id = element.children('.mini-section-btn').attr('data-minisection-btn-id')
                const modal = $(`.modal-mini-section[data-minisection-modal-id="${id}"]`)
                const section = modal.find('.section-mini')

                key = 'minisections'
                value = makeSectionObject(section, index)
            } else if(element.is('.img')) { 
                // image
                key = 'images'
                value = {
                    id: element.attr('data-img-id'),
                    position: index
                }
            } else if(element.is('.wysiwyg-content')) { 
                // wysiwyg
                const editorId = element.attr('data-editor-id')
                key = 'wysiwygs'
                value = {
                    content: editors[editorId].editor.getData(),
                    id: editorId,
                    position: index
                }
            }

            if(key in sectionObj.content) {
                sectionObj.content[key].push(value)
            } else {
                sectionObj.content[key] = [value]
            }
        })
    }

    return sectionObj
}
