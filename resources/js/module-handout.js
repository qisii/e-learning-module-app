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

function showCustomAlert(message) {
    const container = document.getElementById('custom-alert');
    const msg = document.getElementById('custom-alert-message');
    msg.textContent = message;

    container.classList.remove('hidden');

    const okBtn = document.getElementById('custom-alert-ok');
    okBtn.onclick = () => {
        container.classList.add('hidden');
    };
}


/* =========================================================
   Objective → Select Editor → Enable Add Target
========================================================= */

let editorSelectionMode = false;
let activeObjectiveId = null;
let selectedEditorComponentId = null;

/**
 * Enable editor selection mode
 */
document.addEventListener('click', (e) => {
    const selectEditorBtn = e.target.closest('.select-editor-btn');
    if (!selectEditorBtn) return;

    activeObjectiveId = selectEditorBtn.dataset.objectiveId;
    editorSelectionMode = true;
    selectedEditorComponentId = null;

    showCustomAlert('Please select an editor component.');

    // Visual cue: highlight selectable editors
    document.querySelectorAll('.component-block').forEach(el => {
        if (el.querySelector('.suneditor-textarea')) {
            el.classList.add('ring-2', 'ring-blue-400', 'cursor-pointer');
        }
    });
});

/**
 * Handle editor click
 */
document.addEventListener('click', (e) => {
    if (!editorSelectionMode) return;

    const componentBlock = e.target.closest('.component-block');
    if (!componentBlock) return;

    // only text editors
    if (!componentBlock.querySelector('.suneditor-textarea')) return;

    selectedEditorComponentId = componentBlock.dataset.componentId;
    editorSelectionMode = false;

    // cleanup
    document.querySelectorAll('.component-block').forEach(el => {
        el.classList.remove(
            'ring-2',
            'ring-blue-400',
            'cursor-pointer',
            // 'border-gray-200',
            'border-blue-500',
            'border-2',
            'bg-blue-50'
        );
    });

    // emphasize selected editor
    componentBlock.classList.remove('border-gray-200');
    componentBlock.classList.add(
        'border-2',
        'border-blue-500',
        'bg-blue-50'
    );

    // enable Select Target button
    const targetBtn = document.querySelector(
        `.select-target-btn[data-objective-id="${activeObjectiveId}"]`
    );

    if (targetBtn) {
        targetBtn.disabled = false;
        targetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    console.log('Selected editor component:', selectedEditorComponentId);

    showCustomAlert(
        'Editor selected.\n\nNow copy the target text inside the editor, then click "+ Add Target". Then paste the text.'
    );
});

/**
 * Handle + Add Target click
 */
// document.addEventListener('click', (e) => {
//     const addTargetBtn = e.target.closest('.select-target-btn');
//     if (!addTargetBtn || addTargetBtn.disabled) return;

//     const objectiveId = addTargetBtn.dataset.objectiveId;

//     const targetList = document.querySelector(
//         `.target-list[data-objective-id="${objectiveId}"]`
//     );

//     if (!targetList) return;

//     const existingTargets = targetList.querySelectorAll('.target-item').length;

//     // 🔒 LIMIT TO 2 TARGETS
//     if (existingTargets >= 2) {
//         showCustomAlert('You can only add up to 2 targets.');
//         return;
//     }

//     if (existingTargets + 1 >= 2) {
//         addTargetBtn.disabled = true;
//         addTargetBtn.classList.add('opacity-50', 'cursor-not-allowed');
//     }

//     const nextIndex = existingTargets + 1;

//     const wrapper = document.createElement('div');
//     wrapper.className = 'target-item';

//     const textarea = document.createElement('textarea');
//     textarea.className = 'suneditor-textarea';
//     textarea.dataset.editorType = 'target';
//     textarea.dataset.componentId = `${objectiveId}-target-${nextIndex}`;

//     wrapper.appendChild(textarea);
//     targetList.appendChild(wrapper);

//     // initialize ONLY this new editor
//     setTimeout(() => {
//         initSunEditors();
//     }, 0);
// });

document.addEventListener('click', (e) => {
    const addTargetBtn = e.target.closest('.select-target-btn');
    if (!addTargetBtn || addTargetBtn.disabled) return;

    const objectiveId = addTargetBtn.dataset.objectiveId;

    const targetList = document.querySelector(
        `.target-list[data-objective-id="${objectiveId}"]`
    );

    if (!targetList) return;

    const existingTargets = targetList.querySelectorAll('.target-item').length;

    // 🔒 Limit to 2 targets
    if (existingTargets >= 2) {
        showCustomAlert('You can only add up to 2 targets.');
        return;
    }

    const nextIndex = existingTargets + 1;

    // --- wrapper ---
    const wrapper = document.createElement('div');
    wrapper.className = 'target-item flex items-start gap-2';

    // --- textarea ---
    const textarea = document.createElement('textarea');
    textarea.className = 'suneditor-textarea flex-1';
    textarea.dataset.editorType = 'target';
    textarea.dataset.componentId = `${objectiveId}-target-${nextIndex}`;

    // --- delete button ---
    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.className = 'text-red-500 hover:text-red-700 mt-1 delete-target-btn';
    deleteBtn.innerHTML = '<i class="ri-delete-bin-line text-sm"></i>';

    // delete logic
    deleteBtn.addEventListener('click', () => {
        wrapper.remove();

        // re-enable Add Target button
        addTargetBtn.disabled = false;
        addTargetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    });

    wrapper.appendChild(textarea);
    wrapper.appendChild(deleteBtn);
    targetList.appendChild(wrapper);

    // initialize ONLY this new editor
    setTimeout(() => {
        initSunEditors();
    }, 0);

    // disable add button if max reached
    if (existingTargets + 1 >= 2) {
        addTargetBtn.disabled = true;
        addTargetBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
});

// window.saveTargetEditor = function (targetComponentId) {
//     const content = window.getEditorContent?.(targetComponentId);

//     if (content === null || content === undefined) {
//         console.warn('Target editor not ready:', targetComponentId);
//         return;
//     }

//     Livewire.dispatch('debugTargetEditor', {
//         target_id: targetComponentId,
//         content
//     });
// };

// window.saveObjectiveWithTargets = function (objectiveId) {
//     const targetContents = [];

//     // find all target editors for this objective
//     document
//         .querySelectorAll(
//             `.target-list[data-objective-id="${objectiveId}"] .suneditor-textarea`
//         )
//         .forEach((textarea, index) => {
//             const targetId = textarea.dataset.componentId;
//             const content = window.getEditorContent?.(targetId);

//             if (content !== null && content !== undefined) {
//                 targetContents.push({
//                     target_id: targetId,
//                     content: content
//                 });
//             }
//         });

//     Livewire.dispatch('saveObjectiveWithTargets', {
//         objective_id: objectiveId,
//         targets: targetContents
//     });
// };

// window.saveObjectiveWithTargets = function (objectiveId) {
//     const targetContents = [];

//     document
//         .querySelectorAll(`.target-list[data-objective-id="${objectiveId}"] .suneditor-textarea`)
//         .forEach((textarea) => {
//             const targetId = textarea.dataset.componentId;
//             const content = window.getEditorContent?.(targetId);
//             if (content !== null && content !== undefined) {
//                 targetContents.push({ target_id: targetId, content });
//             }
//         });

//     Livewire.dispatch('saveObjectiveWithTargets', {
//         objective_id: objectiveId,
//         targets: targetContents
//     });
// };

window.saveObjectiveWithTargets = function (objectiveId) {
    const targetContents = [];

    document
        .querySelectorAll(
            `.target-list[data-objective-id="${objectiveId}"] .suneditor-textarea`
        )
        .forEach((textarea) => {
            const targetId = textarea.dataset.componentId;
            const content = window.getEditorContent?.(targetId);

            if (content !== null && content !== undefined) {
                targetContents.push({
                    target_id: targetId,
                    content
                });
            }
        });

    Livewire.dispatch('saveObjectiveWithTargets', {
        objective_id: objectiveId,
        selected_editor_component_id: selectedEditorComponentId,
        targets: targetContents
    });
};

document.addEventListener('click', (e) => {
    const selectEditorBtn = e.target.closest('.select-editor-btn');
    if (!selectEditorBtn) return;

    const objectiveId = selectEditorBtn.dataset.objectiveId;
    const targetList = document.querySelector(
        `.target-list[data-objective-id="${objectiveId}"]`
    );

    if (!targetList) return;

    // Remove default targets & placeholder
    targetList.querySelectorAll('.default-target, .default-placeholder')
        .forEach(el => el.remove());

    // If list is empty, show fresh placeholder
    if (!targetList.children.length) {
        targetList.innerHTML = `
            <div class="col-span-2 text-[11px] text-gray-400 italic">
                Select text in the editor to add new targets.
            </div>
        `;
    }

    // Enable "Add Target" button
    const addTargetBtn = document.querySelector(
        `.select-target-btn[data-objective-id="${objectiveId}"]`
    );

    if (addTargetBtn) {
        addTargetBtn.disabled = false;
        addTargetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        addTargetBtn.classList.add('cursor-pointer');
    }
});

// HIDDEN OBJECTVE
// document.addEventListener('DOMContentLoaded', () => {
//     const targets = window.hiddenObjectiveTargets || [];
//     console.log('Hidden targets:', targets);

//     const handoutBlocks = document.querySelectorAll('.handout-text');
//     console.log('Handout blocks:', handoutBlocks.length);

//     if (!targets.length || !handoutBlocks.length) return;

//     const removeZeroWidthSpaces = (html) => html.replace(/\u200B/g, '');

//     handoutBlocks.forEach(container => {
//         // Parse container content into a temporary DOM
//         const tempDiv = document.createElement('div');
//         tempDiv.innerHTML = container.innerHTML;

//         targets.forEach(target => {
//             const normalizedTargetHtml = removeZeroWidthSpaces(target.content);

//             // Iterate through all child nodes and find exact HTML match
//             tempDiv.querySelectorAll('*').forEach(node => {
//                 const nodeHtml = removeZeroWidthSpaces(node.outerHTML);

//                 if (nodeHtml === normalizedTargetHtml) {
//                     console.log('Match found for target:', target.id);

//                     const button = document.createElement('button');
//                     button.className = 'hidden-objective-btn';
//                     button.dataset.targetId = target.id;
//                     button.innerHTML = target.content; // keep formatting
//                     button.onclick = () => handleObjectiveClick(target.id);

//                     node.replaceWith(button);
//                 }
//             });
//         });

//         container.innerHTML = tempDiv.innerHTML;
//     });
// });

// document.addEventListener('DOMContentLoaded', () => {
//     const targets = window.hiddenObjectiveTargets || [];
//     console.log('Hidden targets:', targets);

//     const handoutBlocks = document.querySelectorAll('.handout-text');
//     console.log('Handout blocks:', handoutBlocks.length);

//     if (!targets.length || !handoutBlocks.length) return;

//     const removeZeroWidthSpaces = (html) => html.replace(/\u200B/g, '');

//     handoutBlocks.forEach(container => {
//         const tempDiv = document.createElement('div');
//         tempDiv.innerHTML = container.innerHTML;

//         targets.forEach(target => {
//             const normalizedTargetHtml = removeZeroWidthSpaces(target.content);

//             tempDiv.querySelectorAll('*').forEach(node => {
//                 const nodeHtml = removeZeroWidthSpaces(node.outerHTML);

//                 if (nodeHtml === normalizedTargetHtml) {
//                     console.log('Match found for target:', target.id);

//                     const button = document.createElement('button');
//                     button.className = 'hidden-objective-btn';
//                     button.dataset.targetId = target.id;
//                     button.innerHTML = target.content; // preserve formatting

//                     node.replaceWith(button);
//                 }
//             });
//         });

//         container.innerHTML = tempDiv.innerHTML;
//     });
// });

// document.addEventListener('DOMContentLoaded', () => {
//     const targets = window.hiddenObjectiveTargets || [];
//     console.log('Hidden targets:', targets);

//     const handoutBlocks = document.querySelectorAll('.handout-text');
//     console.log('Handout blocks:', handoutBlocks.length);

//     if (!targets.length || !handoutBlocks.length) return;

//     const removeZeroWidthSpaces = (str) => str.replace(/\u200B/g, '');

//     handoutBlocks.forEach(container => {
//         const tempDiv = document.createElement('div');
//         tempDiv.innerHTML = container.innerHTML;

//         targets.forEach(target => {
//             const targetText = removeZeroWidthSpaces(target.content.replace(/<[^>]+>/g, '').trim());

//             tempDiv.querySelectorAll('*').forEach(node => {
//                 const nodeText = removeZeroWidthSpaces(node.textContent.trim());

//                 if (nodeText === targetText) {
//                     console.log('Match found for target:', target.id);

//                     const button = document.createElement('button');
//                     button.className = 'hidden-objective-btn';
//                     button.dataset.targetId = target.id;
//                     button.innerHTML = node.innerHTML; // preserve formatting

//                     node.replaceWith(button);
//                 }
//             });
//         });

//         container.innerHTML = tempDiv.innerHTML;
//     });
// });

document.addEventListener('DOMContentLoaded', () => {
    const targets = window.hiddenObjectiveTargets || [];
    console.log('Hidden targets:', targets);

    const handoutBlocks = document.querySelectorAll('.handout-text');
    console.log('Handout blocks:', handoutBlocks.length);

    if (!targets.length || !handoutBlocks.length) return;

    const normalize = (str) =>
        str
            .replace(/\u200B/g, '')
            .replace(/\s+/g, ' ')
            .trim();

    handoutBlocks.forEach(container => {
        const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);

        let textNode;
        const nodesToReplace = [];

        while (textNode = walker.nextNode()) {
            const parent = textNode.parentNode;

            if (parent.closest('.hidden-objective-btn')) continue;

            targets.forEach(target => {
                const targetText = normalize(target.content.replace(/<[^>]+>/g, ''));

                if (!targetText) return;

                const nodeText = normalize(textNode.textContent);

                const index = nodeText.indexOf(targetText);
                if (index === -1) return;

                nodesToReplace.push({ textNode, target, start: index, length: targetText.length });
            });
        }

        nodesToReplace.forEach(({ textNode, target, start, length }) => {
            const originalText = Array.from(textNode.textContent); // split into Unicode-aware array
            const beforeText = originalText.slice(0, start).join('');
            const matchedText = originalText.slice(start, start + length).join('');
            const afterText = originalText.slice(start + length).join('');

            const before = document.createTextNode(beforeText);
            const matchNode = document.createTextNode(matchedText);
            const after = document.createTextNode(afterText);

            const button = document.createElement('button');
            button.className = 'hidden-objective-btn';
            button.dataset.targetId = target.id;
            button.appendChild(matchNode);

            textNode.replaceWith(before, button, after);
        });

    });
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('.hidden-objective-btn');
    if (!btn) return;

    const targetId = btn.dataset.targetId;
    handleObjectiveClick(targetId);
});

function handleObjectiveClick(targetId) {
    const target = window.hiddenObjectiveTargets.find(t => t.id === targetId);

    if (!target) {
        console.warn('No completion message found for target:', targetId);
        return;
    }

    showObjectiveDialog(target.completion_message);
}

// function showObjectiveDialog(message) {
//     const dialog = document.getElementById('objective-dialog');
//     const msg = document.getElementById('objective-dialog-message');
//     const okBtn = document.getElementById('objective-dialog-ok');

//     msg.textContent = message;

//     // Show dialog
//     dialog.classList.remove('hidden');
//     dialog.classList.add('flex');

//     launchConfetti(); // 🎉

//     // Bind close (same pattern as custom alert)
//     okBtn.onclick = () => {
//         dialog.classList.add('hidden');
//         dialog.classList.remove('flex');
//     };
// }

function showObjectiveDialog(message) {
    const dialog = document.getElementById('objective-dialog');
    const box = document.getElementById('objective-dialog-box');
    const msg = document.getElementById('objective-dialog-message');
    const okBtn = document.getElementById('objective-dialog-ok');

    msg.textContent = message;

    // Reset state
    box.classList.remove('objective-dialog-close');
    box.classList.add('objective-dialog-open');

    dialog.classList.remove('hidden');
    dialog.classList.add('flex');

    launchConfetti(); // 🎉

    okBtn.onclick = () => {
        closeObjectiveDialog();
    };
}

// function closeObjectiveDialog() {
//     const dialog = document.getElementById('objective-dialog');
//     const box = document.getElementById('objective-dialog-box');

//     // Play close animation
//     box.classList.remove('objective-dialog-open');
//     box.classList.add('objective-dialog-close');

//     // Hide after animation ends
//     setTimeout(() => {
//         dialog.classList.add('hidden');
//         dialog.classList.remove('flex');
//     }, 250); // match CSS duration
// }

// function closeObjectiveDialog() {
//     const dialog = document.getElementById('objective-dialog');
//     const box = document.getElementById('objective-dialog-box');

//     // Play close animation
//     box.classList.remove('objective-dialog-open');
//     box.classList.add('objective-dialog-close');

//     // Hide dialog after animation ends
//     setTimeout(() => {
//         dialog.classList.add('hidden');
//         dialog.classList.remove('flex');

//         // SHOW PAGINATION AFTER OBJECTIVE COMPLETION
//         const pagination = document.getElementById('pagination-wrapper');
//         if (pagination) {
//             pagination.classList.remove('hidden');
//             pagination.classList.add('flex', 'justify-ontecenter'); // optional styling
//         }
//     }, 250); // match CSS duration
// }

// function closeObjectiveDialog() {
//     const dialog = document.getElementById('objective-dialog');
//     const box = document.getElementById('objective-dialog-box');

//     box.classList.remove('objective-dialog-open');
//     box.classList.add('objective-dialog-close');

//     setTimeout(() => {
//         dialog.classList.add('hidden');
//         dialog.classList.remove('flex');

//         // SHOW PAGINATION AFTER OBJECTIVE COMPLETION
//         const pagination = document.getElementById('pagination-wrapper');
//         if (pagination) {
//             pagination.classList.remove('hidden');
//             // pagination.classList.add('flex', 'justify-center'); 
//         }

//         // HIDE hidden objectives after completion
//         const hiddenObjectivesSection = document.querySelector('#module-wrapper .hidden-objective-glow')?.closest('div.mt-12');
//         if (hiddenObjectivesSection) hiddenObjectivesSection.style.display = 'none';

//     }, 250);
// }

function closeObjectiveDialog() {
    const dialog = document.getElementById('objective-dialog');
    const box = document.getElementById('objective-dialog-box');

    box.classList.remove('objective-dialog-open');
    box.classList.add('objective-dialog-close');

    setTimeout(() => {
        dialog.classList.add('hidden');
        dialog.classList.remove('flex');

        // SHOW PAGINATION AFTER OBJECTIVE COMPLETION
        const pagination = document.getElementById('pagination-wrapper');
        if (pagination) {
            pagination.classList.remove('hidden');
        }

        // HIDE hidden objectives after completion
        const hiddenObjectivesSection = document.querySelector('#module-wrapper .hidden-objective-glow')?.closest('div.mt-12');
        if (hiddenObjectivesSection) hiddenObjectivesSection.style.display = 'none';

        // SHOW BOTTOM BUTTONS IF LAST PAGE
        const bottomButtons = document.getElementById('bottom-buttons-wrapper');
        if (bottomButtons) bottomButtons.style.display = 'block';

    }, 250);
}

function launchConfetti() {
    const canvas = document.getElementById('confetti-canvas');
    if (!canvas) return;

    canvas.classList.remove('hidden');

    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const confettiCount = 120;
    const confetti = [];

    const colors = ['#f97316', '#facc15', '#22c55e', '#38bdf8', '#a855f7'];

    for (let i = 0; i < confettiCount; i++) {
        confetti.push({
            x: canvas.width / 2,
            y: canvas.height / 2,
            r: Math.random() * 6 + 4,
            d: Math.random() * confettiCount,
            color: colors[Math.floor(Math.random() * colors.length)],
            tilt: Math.random() * 10 - 10,
            tiltAngleIncrement: Math.random() * 0.1 + 0.05,
            tiltAngle: 0,
            vx: Math.random() * 6 - 3,
            vy: Math.random() * -6 - 2,
            gravity: 0.15
        });
    }

    let frame = 0;

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        confetti.forEach((c, i) => {
            ctx.beginPath();
            ctx.lineWidth = c.r;
            ctx.strokeStyle = c.color;
            ctx.moveTo(c.x + c.tilt + c.r / 2, c.y);
            ctx.lineTo(c.x + c.tilt, c.y + c.tilt + c.r / 2);
            ctx.stroke();
        });

        update();
        frame++;

        if (frame < 120) {
            requestAnimationFrame(draw);
        } else {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            canvas.classList.add('hidden');
        }
    }

    function update() {
        confetti.forEach(c => {
            c.x += c.vx;
            c.y += c.vy;
            c.vy += c.gravity;
            c.tiltAngle += c.tiltAngleIncrement;
            c.tilt = Math.sin(c.tiltAngle) * 15;
        });
    }

    draw();
}
