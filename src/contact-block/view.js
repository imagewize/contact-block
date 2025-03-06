/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/* eslint-disable no-console */
console.log( 'Hello World! (from create-block-contact-block block)' );
/* eslint-enable no-console */

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('[data-contact-form]');

    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });

    function handleFormSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const messageContainer = form.querySelector('[data-form-message]');
        const submitButton = form.querySelector('.submit-button');
        const formData = new FormData(form);
        
        // Add action for WordPress AJAX handling
        formData.append('action', 'contact_form_submission');
        
        // Disable button and show loading state
        submitButton.disabled = true;
        submitButton.innerText = 'Sending...';
        
        fetch(window.contactFormData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageContainer.innerHTML = `<div class="success-message">${data.data.message}</div>`;
                form.reset();
            } else {
                messageContainer.innerHTML = `<div class="error-message">${data.data.message}</div>`;
            }
        })
        .catch(error => {
            messageContainer.innerHTML = `<div class="error-message">An error occurred. Please try again later.</div>`;
            console.error('Error:', error);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerText = submitButton.dataset.originalText || 'Submit';
        });
    }
});
