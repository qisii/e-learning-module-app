// module-handout.js
export function initAllDraggables() {
    const container = document.querySelector('.pages-sortable');
    if (!container) return;

    if (container._sortable) container._sortable.destroy();
    container._sortable = new Sortable(container, {
        animation: 150,
        handle: '.page-drag-handle',
        ghostClass: 'bg-blue-50',
        onSort: () => {
            const ordered = Array.from(container.querySelectorAll('[data-page-id]'))
                .map(el => Number(el.dataset.pageId));
            Livewire.dispatch('reorderPages', { orderedPageIds: ordered });
        }
    });

    document.querySelectorAll('.components-list').forEach(listEl => {
        const pageId = Number(listEl.dataset.pageId);
        if (listEl._sortable) listEl._sortable.destroy();
        listEl._sortable = new Sortable(listEl, {
            group: { name: 'shared', pull: false, put: ['shared'] },
            animation: 150,
            onAdd(evt) {
                const type = evt.item.dataset.type || evt.item.textContent.trim().toLowerCase();
                evt.item.remove();
                Livewire.dispatch('addComponentFromPalette', pageId, type);
            },
            onSort() {
                const ordered = Array.from(listEl.querySelectorAll('.component-block'))
                    .map(el => Number(el.dataset.componentId));
                Livewire.dispatch('reorderComponents', { pageId, orderedIds: ordered });
            }
        });
    });

    const virtual = document.createElement('div');
    virtual.style.display = 'none';
    virtual.classList.add('virtual-palette');
    document.body.appendChild(virtual);
    document.querySelectorAll('.palette-item').forEach(el => {
        const v = document.createElement('div');
        v.className = 'virtual-item';
        v.dataset.type = el.dataset.type;
        v.textContent = el.textContent.trim();
        virtual.appendChild(v);
    });
    new Sortable(virtual, {
        group: { name: 'shared', pull: 'clone', put: false },
        sort: false,
        animation: 150,
    });
}

// Re-initialize after Livewire updates
Livewire.hook('message.processed', () => {
    initAllDraggables();
});
