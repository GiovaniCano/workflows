import { draggableOptionsBase } from '../_jquery_ui'
import { insertAddButton, insertDeleteButton, insertDragButton } from './_edit-buttons'

/* 
    CKEditor and translation is being pushed from views/workflows/form.blade.php
*/

const maxLength = 8000
const locale = $('meta[name="locale"]').attr('content');
/**
 * @type EditorConfig
 */
const editorConfig = {
    // licenseKey: '',
    language: locale,
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h3', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h4', title: 'Heading 2', class: 'ck-heading_heading2' }
        ]
    }
}

/**
 * @param {JQuery<HTMLElement>} wysiwyg
 * @param {number|string} editorId Id of the editor instance
 * @returns void
 */
export function initializeWysiwyg(wysiwyg, editorId = null) {
    insertDeleteButton(wysiwyg.find('.wysiwyg-content-buttons'))
    insertDragButton(wysiwyg.find('.wysiwyg-content-buttons'))
    insertAddButton(wysiwyg)
    wysiwyg.draggable(draggableOptionsBase)

    // char counter
    const charCounter = wysiwyg.find('.wysiwyg-char-counter')
    const counter = wysiwyg.find('.wysiwyg-char-counter span').eq(0)
    charCounter.find('span').eq(1).text(maxLength)

    editorConfig.wordCount = {
        onUpdate: stats => {
            const count = stats.characters
            counter.text(count)
            if(count > maxLength) { // invalid
                wysiwyg.addClass('invalid')
                if(editors[editorId]) editors[editorId].valid = false // var editors in views/workflows/form
            } else { // valid
                wysiwyg.removeClass('invalid')
                if(editors[editorId]) editors[editorId].valid = true // var editors in views/workflows/form
            }
        }
    }

    // editor
    const editor = wysiwyg.find('.editor').get(0)

    ClassicEditor.create(editor, editorConfig)
        .then(editor => {
            wysiwyg.show()

            if(editorId) {
                wysiwyg.attr('data-editor-id', editorId)
            } else {
                editorId = wysiwyg.attr('data-editor-id')
            }
            
            editors[editorId] = { // var editors in views/workflows/form
                editor: editor,
                valid: true,
            }
        })
        .catch(error => console.error(error))
}