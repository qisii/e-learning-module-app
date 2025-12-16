import Sortable from 'sortablejs';
import { Draggable, Droppable } from '@shopify/draggable';

let sortables = [];

/* ---------- destroy & recreate ---------- */
function destroyAllSortables() {
    if (!Array.isArray(sortables)) sortables = [];
    while (sortables.length) {
        const s = sortables.pop();
        try {
            s.destroy();
        } catch (e) {
            /* ignore */
        }
    }
}

/* ---------- initialize all ---------- */
function initSortables() {
    destroyAllSortables();

    /* ---------- Pages sortable ---------- */
    const pagesContainer = document.querySelector('.pages-sortable');
    if (pagesContainer) {
        const pagesSortable = new Sortable(pagesContainer, {
            animation: 150,
            handle: '.page-drag-handle',
            ghostClass: 'bg-blue-50',

            onSort() {
                const orderedPageIds = Array.from(pagesContainer.children)
                    .map(el => Number(el.dataset.pageId));

                Livewire.dispatch('reorderPages', {
                    orderedPageIds
                });
            }
        });

        sortables.push(pagesSortable);
    }

    /* ---------- Components sortable (per page) ---------- */
    document.querySelectorAll('.components-list').forEach(listEl => {
        const compSortable = new Sortable(listEl, {
            group: {
                name: 'components',
                pull: true,
                put: ['components', 'palette']
            },
            animation: 150,
            ghostClass: 'bg-blue-50',
            fallbackOnBody: true,
            swapThreshold: 0.65,

            onAdd(evt) {
                // dragged from palette
                if (evt.from?.classList.contains('palette')) {
                    const type = evt.item.dataset.type;
                    const pageId = Number(evt.to.dataset.pageId);

                    Livewire.dispatch('addComponentFromPalette', {
                        pageId,
                        type
                    });

                    // remove cloned element
                    evt.item.remove();
                }
            },

            onEnd(evt) {
                const pageId = Number(evt.to.dataset.pageId);
                const orderedComponentIds = Array.from(evt.to.children)
                    .map(el => Number(el.dataset.componentId))
                    .filter(Boolean);

                Livewire.dispatch('reorderComponents', {
                    pageId,
                    orderedComponentIds
                });
            }
        });

        sortables.push(compSortable);
    });

    /* ---------- Palette (clone only) ---------- */
    const palette = document.querySelector('.palette');
    if (palette) {
        const paletteSortable = new Sortable(palette, {
            group: {
                name: 'palette',
                pull: 'clone',
                put: false
            },
            sort: false,
            animation: 150,
            ghostClass: 'bg-blue-50'
        });

        sortables.push(paletteSortable);
    }
}

/* ---------- Expose helpers ---------- */
window.saveTextComponent = function (componentId) {
    const content = window.getEditorContent?.(componentId);

    if (content === null || content === undefined) {
        console.warn('Editor not ready for component:', componentId);
        return;
    }

    Livewire.dispatch('saveTextComponent', {
        component_id: componentId,
        content
    });
};

/* ---------- boot ---------- */
document.addEventListener('DOMContentLoaded', () => {
    initSortables();
});

/* ---------- Livewire refresh ---------- */
window.addEventListener('sortable:refresh', () => {
    setTimeout(() => initSortables(), 10);
});
