// Notes-specific JavaScript
import './bootstrap';

// Note editor functionality
document.addEventListener('DOMContentLoaded', function() {
    // Toggle note preview
    const previewButtons = document.querySelectorAll('.note-preview-toggle');
    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const noteId = this.dataset.noteId;
            const preview = document.querySelector(`.note-preview-${noteId}`);
            const editor = document.querySelector(`.note-editor-${noteId}`);

            preview.classList.toggle('hidden');
            editor.classList.toggle('hidden');
            this.textContent = preview.classList.contains('hidden') ? 'Preview' : 'Edit';
        });
    });

    // Markdown toggles
    const markdownToggles = document.querySelectorAll('.markdown-toggle');
    markdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const textarea = document.querySelector(`#${this.dataset.target}`);
            const text = textarea.value;

            // Toggle markdown formatting
            if (this.dataset.action === 'bold') {
                textarea.value = text + ' **bold text**';
            } else if (this.dataset.action === 'italic') {
                textarea.value = text + ' *italic text*';
            } else if (this.dataset.action === 'link') {
                textarea.value = text + ' [link text](url)';
            }

            textarea.focus();
        });
    });
});
