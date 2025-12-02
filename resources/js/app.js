import './bootstrap';
import SUNEDITOR from 'suneditor';
import 'suneditor/dist/css/suneditor.min.css';
import * as plugins from 'suneditor/src/plugins';

// ---------- SUNEDITOR destroy & recreate pattern ----------
let sunEditors = []; // global array to track editors

function destroyAllSunEditors() {
    while (sunEditors.length) {
        const editor = sunEditors.pop();
        try {
            editor.destroy();
        } catch (e) {
            console.warn("Error destroying SunEditor:", e);
        }
    }

    // Also clear initialized flags so new textareas can be initialized
    document.querySelectorAll('.suneditor-textarea').forEach(t => t.removeAttribute('data-initialized'));
}

function initSunEditors() {
    destroyAllSunEditors(); // always destroy existing editors first

    document.querySelectorAll('.suneditor-textarea').forEach(textarea => {
        if (textarea.dataset.initialized) return;

        const editor = SUNEDITOR.create(textarea.id, {
            width: '100%',
            height: 220,
            plugins,
            buttonList: [
                [
                    'undo', 'redo',
                    'font', 'fontSize',
                    'formatBlock', 
                    'paragraphStyle',
                    'blockquote',
                    'bold', 'underline', 'italic', 'strike', 'subscript', 'superscript',
                    'fontColor', 'hiliteColor', 'textStyle',
                    'align', 'horizontalRule', 'list', 'lineHeight',
                    'table', 'link', 'image', 'audio',
                    'fullScreen', 'showBlocks', 'codeView'
                ]
            ],
        });

        textarea.dataset.initialized = 'true';
        sunEditors.push(editor);
        console.log("SunEditor loaded:", editor);
    });
}

// ---------- Initialize on DOM ready ----------
document.addEventListener('DOMContentLoaded', () => {
    initSunEditors();
});


// ---------- Custom SunEditor refresh event ----------
window.addEventListener('suneditor:refresh', () => {
    console.log('SunEditor refresh event received');
    setTimeout(() => initSunEditors(), 20); // slight delay to ensure DOM is ready
});
