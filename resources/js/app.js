import './bootstrap';
import SUNEDITOR from 'suneditor';
import 'suneditor/dist/css/suneditor.min.css';
import * as plugins from 'suneditor/src/plugins';

// ---------- SUNEDITOR destroy & recreate pattern ----------
// map of componentId -> editor instance
const sunEditorsMap = {};

// destroy all editors and clear map
function destroyAllSunEditors() {
  Object.keys(sunEditorsMap).forEach(id => {
    try {
      sunEditorsMap[id].destroy();
    } catch (e) {
      console.warn('Error destroying SunEditor', id, e);
    }
    delete sunEditorsMap[id];
  });

  // remove initialized flags on textareas
  document.querySelectorAll('.suneditor-textarea').forEach(t => t.removeAttribute('data-initialized'));
}

// init all text editors currently in DOM
function initSunEditors() {
  // destroy existing so we always re-create fresh
  destroyAllSunEditors();

  document.querySelectorAll('.suneditor-textarea').forEach(textarea => {
    // each textarea must have data-component-id attribute
    const componentId = textarea.dataset.componentId;
    if (!componentId) return;

    // avoid double init (defensive)
    if (textarea.dataset.initialized) return;

    // create editor
    const editorType = textarea.dataset.editorType || 'main';

    const editor = SUNEDITOR.create(textarea, {
      width: '100%',
      height: editorType === 'target' ? '15px' : 500,
      plugins,
      buttonList: editorType === 'target'
        ? []
        : [
            [
              'undo', 'redo',
              'font', 'fontSize',
              'formatBlock',
              'paragraphStyle',
              'blockquote',
              'bold', 'underline', 'italic', 'strike',
              'fontColor', 'hiliteColor',
              'align', 'list',
              'table', 'link', 'image',
              'fullScreen', 'codeView'
            ]
          ],
      resizingBar: editorType !== 'target',
    });

    // store reference
    sunEditorsMap[componentId] = editor;

    // mark DOM as initialized
    textarea.dataset.initialized = 'true';

  });
}

// helper to get content by id (returns HTML string or null)
function getEditorContent(componentId) {
  const ed = sunEditorsMap[componentId];
  if (!ed) return null;
  try {
    // getContents returns HTML string
    return ed.getContents();
  } catch (e) {
    console.warn('Error getting content for', componentId, e);
    return null;
  }
}

// Expose helper globally (used by your inline save button)
window.getEditorContent = getEditorContent;
window.initSunEditors = initSunEditors;
window.destroyAllSunEditors = destroyAllSunEditors;

document.addEventListener('DOMContentLoaded', () => initSunEditors());

// Livewire updates finished → refresh editors
Livewire.hook('message.processed', () => {
    setTimeout(() => initSunEditors(), 10);
});

// Also refresh when you manually dispatch (add component, reorder, etc.)
window.addEventListener('suneditor:refresh', () => {
    setTimeout(() => initSunEditors(), 10);
});

// USER HELP MODAL
document.addEventListener('DOMContentLoaded', () => {
    const helpBtn = document.getElementById('help-btn');
    const modal = document.getElementById('help-modal');
    const modalBox = document.getElementById('help-modal-box');
    const closeBtn = document.getElementById('help-modal-close');
    const okBtn = document.getElementById('help-modal-ok');

    if (!helpBtn || !modal) return;

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'help-backdrop-show');
        modal.classList.remove('help-backdrop-hide');

        modalBox.classList.add('help-modal-show');
        modalBox.classList.remove('help-modal-hide');
    }

    function closeModal() {
        modal.classList.add('help-backdrop-hide');
        modal.classList.remove('help-backdrop-show');

        modalBox.classList.add('help-modal-hide');
        modalBox.classList.remove('help-modal-show');

        // Wait for animation to finish before hiding
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    helpBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openModal();
    });

    closeBtn.addEventListener('click', closeModal);
    okBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const helpBtn = document.getElementById('admin-help-btn');
    const modal = document.getElementById('admin-help-modal');
    const modalBox = document.getElementById('admin-help-modal-box');
    const closeBtn = document.getElementById('admin-help-modal-close');
    const okBtn = document.getElementById('admin-help-modal-ok');

    if (!helpBtn || !modal) return;

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'help-backdrop-show');
        modalBox.classList.add('help-modal-show');
    }

    function closeModal() {
        modal.classList.add('help-backdrop-hide');
        modalBox.classList.add('help-modal-hide');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.classList.remove('help-backdrop-show', 'help-backdrop-hide');
            modalBox.classList.remove('help-modal-show', 'help-modal-hide');
        }, 200);
    }

    helpBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openModal();
    });

    closeBtn.addEventListener('click', closeModal);
    okBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
});