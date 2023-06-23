import { insertAddButton, insertDeleteButton, insertDragButton } from './_edit-buttons'

/* 
    CKEditor and translation is being pushed from views/workflows/form.blade.php
*/

/**
 * @param {JQuery<HTMLElement>} wysiwyg
 * @returns void
 */
export function initializeWysiwyg(wysiwyg) {
    insertDeleteButton(wysiwyg.find('.wysiwyg-content-buttons'))
    insertDragButton(wysiwyg.find('.wysiwyg-content-buttons'))
    insertAddButton(wysiwyg)
    
    const editor = wysiwyg.find('.editor').get(0)
    const locale = $('meta[name="locale"]').attr('content');
    ClassicEditor.create(editor, {
        // licenseKey: '',
        language: locale,
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h3', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h4', title: 'Heading 2', class: 'ck-heading_heading2' }
            ]
        },
    })
    .then(editor => {
        // window.editor = editor
        wysiwyg.show()
    })
    .catch(error => console.error(error))
}