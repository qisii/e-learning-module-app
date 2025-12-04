<div>
    <div class="w-full mx-auto grid grid-cols-1 lg:grid-cols-7 lg:gap-6">
        {{-- Left palette --}}
        <div class="bg-white rounded-lg shadow-md p-4 mb-5 lg:col-span-1 w-full h-fit sticky top-5 z-10">
            <h3 class="text-sm font-semibold text-gray-500 mb-2" style="font-family: 'Poppins', sans-serif;">
                Components
            </h3>

            {{-- Add Page button --}}
            <div class="mb-4">
                <div class="flex items-center justify-center font-secondary">
                    <button 
                        wire:click.prevent="addPage" 
                        class="flex items-center w-full py-3 px-3 border-2 border-blue-400 rounded-lg hover:bg-blue-50 transition"
                    >
                        <i class="ri-add-line text-blue-500 text-lg mr-3"></i>
                        <span class="text-blue-600 font-medium text-[11px]">
                            Add Page
                        </span>
                    </button>
                </div>
            </div>

            {{-- Component buttons --}}
            <div class="palette space-y-2 font-secondary text-[13px]">
                <div class="palette-item flex items-center py-3 px-3 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 cursor-grab" data-type="text">
                    <i class="ri-text align-middle text-gray-500 text-lg mr-3"></i>
                    <span class="text-gray-700 font-medium text-[11px]">Editor</span>
                </div>

                <div class="palette-item flex items-center py-3 px-3 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 cursor-grab" data-type="audio">
                    <i class="ri-lightbulb-flash-line text-gray-500 text-lg mr-3"></i>
                    <span class="text-gray-700 font-medium text-[11px]">Hidden Objective</span>
                </div>
            </div>
            
        </div>

        {{-- Main area --}}
        <div class="lg:col-span-6 col-span-1">
            <div class="bg-[#DBEAFE] border border-blue-300 text-blue-800 rounded-lg py-4 px-5 mb-5">
                <div class="flex items-center">
                    <i class="ri-information-fill text-blue-600 text-lg mr-2"></i>
                    <h2 class="text-[15px] font-semibold" style="font-family: 'Poppins', sans-serif;">Please Note</h2>
                </div>
                <ul class="text-[13px] leading-relaxed list-disc pl-5" style="font-family: 'Inter', sans-serif;">
                    <li>
                        When using links, please change their font color. They will appear black to students by default.
                    </li>
                    <li>
                        For adding audio, please insert the audio URL using the link tool  
                        <i class="ri-link-unlink text-red-600 inline-block"></i>  
                        inside the editor.
                    </li>
                    <li>
                        After adding or editing content, please make sure to click the
                        <span class="inline-flex items-center gap-1 text-green-600">
                            <i class="ri-checkbox-circle-line"></i> Save
                        </span>
                        button to apply the changes.
                    </li>
                </ul>
            </div>

            {{-- Score Area --}}
            <div class="bg-white shadow-lg rounded-lg px-5 py-3 mb-6 font-secondary">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-gray-700 text-sm">
                            This module handout will display if the student's pretest score is
                        </span>
                        <input 
                            type="number"
                            wire:model.defer="handoutScore"
                            class="w-20 rounded-md font-bold border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2 text-sm"
                            placeholder="0"
                        />
                    </div>

                    <div wire:click="saveHandoutScore" class="hover:text-green-700 text-green-500 ms-auto text-[13px] cursor-pointer">
                        <button class="flex items-center gap-1">
                            <i class="ri-checkbox-circle-line"></i> Save
                        </button>
                    </div>
                </div>
            </div>

            {{-- Handout Area --}}
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6 h-[1000px] overflow-auto no-scrollbar">
                <div class="text-center mb-10">
                    <h2 class="text-lg font-bold text-gray-800 font-secondary">Module Handout</h2>
                    <span class="inline-block mt-2 px-4 py-1 bg-purple-100 text-purple-600 text-sm font-semibold rounded-full font-secondary">
                        @if ($level_id == 1) Easy
                        @elseif ($level_id == 2) Average
                        @elseif ($level_id == 3) Hard
                        @else Unknown Level
                        @endif
                    </span>
                </div>

                {{-- Components Section --}}
                <div class="pages-sortable space-y-4 font-secondary text-sm">
                    @forelse ($this->pages as $page)
                        <div class="border-2 border-gray-200 rounded-lg p-4 page-drag-handle" data-page-id="{{ $page->id }}" wire:key="handout-page-{{ $page->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="ri-drag-move-2-line text-xl text-gray-400 cursor-grab page-drag-handle"></i>
                                    <span class="font-semibold">Page {{ $page->page_number }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="removePage({{ $page->id }})" class="text-red-500 hover:text-red-700">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Components drop area --}}
                            <div class="components-list min-h-auto" data-page-id="{{ $page->id }}">
                                @foreach ($page->components as $component)
                                    <div class="component-block border border-gray-200 p-3 rounded bg-white flex items-start gap-3 cursor-grab"
                                        data-component-id="{{ $component->id }}" wire:key="handout-component-{{ $component->id }}">

                                        {{-- Component content --}}
                                        <div class="flex-1 font-medium text-gray-700">

                                            {{-- TEXT BLOCK --}}
                                            @if ($component->type === 'text')
                                                <div class="flex justify-between items-center mb-2">

                                                    <div class="hover:text-green-700 text-green-500 ms-auto text-[13px]">
                                                        <button
                                                            onclick="saveTextComponent({{ $component->id }})"
                                                            type="button"
                                                            class="cursor-pointer"
                                                        >
                                                            <i class="ri-checkbox-circle-line"></i> Save
                                                        </button>
                                                    </div>

                                                    <button wire:click.prevent="removeComponent({{ $component->id }})"
                                                            class="text-red-500 hover:text-red-700">
                                                        <i class="ri-delete-bin-line text-sm ms-2"></i>
                                                    </button>
                                                </div>

                                                <div wire:ignore.self>
                                                    <textarea
                                                        id="suneditor-{{ $component->id }}"
                                                        class="suneditor-textarea"
                                                        data-component-id="{{ $component->id }}"
                                                    >{{ optional(json_decode($component->data, true))['content'] ?? '' }}</textarea>
                                                </div>
                                            {{-- AUDIO BLOCK --}}
                                            @elseif ($component->type === 'audio')
                                                <div class="flex justify-between items-center mb-2">
                                                    <div class="text-[13px] text-gray-700">
                                                        Audio block {{ $component->sort_order }}
                                                    </div>

                                                    <button wire:click.prevent="removeComponent({{ $component->id }})"
                                                            class="text-red-500 hover:text-red-700">
                                                        <i class="ri-delete-bin-line text-sm"></i>
                                                    </button>
                                                </div>

                                                <!-- Your audio UI here -->
                                            {{-- UNKNOWN BLOCK --}}
                                            @else
                                                <div class="flex justify-between items-center mb-2">
                                                    <div class="text-[13px] text-gray-700">
                                                        Unknown component
                                                    </div>

                                                    <button wire:click.prevent="removeComponent({{ $component->id }})"
                                                            class="text-red-500 hover:text-red-700">
                                                        <i class="ri-delete-bin-line text-sm"></i>
                                                    </button>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 font-secondary">
                            No pages yet. Click <strong>+ Add Page</strong> to start.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    {{-- Shopify Draggable and Droppable --}}
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.11/lib/draggable.bundle.js"></script>

    <script>
    /* ---------- sortable control (destroy & recreate pattern) ---------- */

    let sortables = [];

    // Destroy all active Sortable instances
    function destroyAllSortables() {
        if (!Array.isArray(sortables)) sortables = [];
        while (sortables.length) {
            const s = sortables.pop();
            try { s.destroy(); } catch (e) { /* ignore */ }
        }
    }

    // Initialize all sortables (pages, components, palette)
    function initSortables() {
        destroyAllSortables();

        // --- Pages sortable ---
        const pagesContainer = document.querySelector('.pages-sortable');
        if (pagesContainer) {
            const pagesSortable = new Sortable(pagesContainer, {
                animation: 150,
                handle: '.page-drag-handle',
                ghostClass: 'bg-blue-50',
                onSort: function () {
                    const ordered = Array.from(pagesContainer.children)
                        .map(el => Number(el.dataset.pageId));
                    // call Livewire method (server-side)
                    Livewire.dispatch('reorderPages', { orderedPageIds: ordered });
                }
            });
            sortables.push(pagesSortable);
        }

        // --- Components sortables (one per page) ---
        document.querySelectorAll('.components-list').forEach(listEl => {
            const compSortable = new Sortable(listEl, {
                group: { name: 'components', pull: true, put: ['components', 'palette'] },
                animation: 150,                  // smooth animation
                ghostClass: 'bg-blue-50',
                fallbackOnBody: true,
                swapThreshold: 0.65,

                onAdd(evt) {
                    // if dragged from palette into a page
                    if (evt.from && evt.from.classList && evt.from.classList.contains('palette')) {
                        const type = evt.item.dataset.type;
                        const pageId = Number(evt.to.dataset.pageId);

                        // create server-side component
                        Livewire.dispatch('addComponentFromPalette', {
                            pageId: pageId,
                            type: type
                        });

                        // remove the cloned helper element inserted by Sortable
                        evt.item.remove();
                    }
                },

                onEnd(evt) {
                    // After drag completes, persist order for the destination page
                    const newPageId = Number(evt.to.dataset.pageId);
                    const orderedComponentIds = Array.from(evt.to.children)
                        .map(child => Number(child.dataset.componentId) || null)
                        .filter(Boolean);

                    Livewire.dispatch('reorderComponents', {
                        pageId: newPageId,
                        orderedComponentIds: orderedComponentIds
                    });
                }
            });

            sortables.push(compSortable);
        });

        // --- Palette (source of cloned items) ---
        const palette = document.querySelector('.palette');
        if (palette) {
            const paletteSortable = new Sortable(palette, {
                group: { name: 'palette', pull: 'clone', put: false },
                sort: false,
                animation: 150,                  // smooth animation
                ghostClass: 'bg-blue-50',
            });
            sortables.push(paletteSortable);
        }
    }

    function saveTextComponent(componentId) {
  const content = window.getEditorContent(componentId);
  console.log('saveTextComponent -> content for', componentId, content);

  if (content === null) {
    // helpful debug message if editor not ready
    console.warn('Editor not found for component', componentId);
    return;
  }

  Livewire.dispatch('saveTextComponent', {
    component_id: componentId,    // use snake keys if your dd showed that
    content: content
  });
}

    /* Initialize on first load */
    document.addEventListener('DOMContentLoaded', () => {
        initSortables();
    });

    /* Livewire v3: listen for our explicit refresh event */
    window.addEventListener('sortable:refresh', () => {
        console.log('Sortable refresh event received');
        // small microtask timing safety (usually not needed, but safe)
        setTimeout(() => initSortables(), 10);
    });
    
    </script>
</div>


{{-- 
can we try this approach?

In js, It will send an array of page number everytime a page is added or sorted.
In livewire class, before updating the reordering, it will get all handut pages and then update the page_number based on the reorder pages from the blade.

For example:

FIRST ADD:
Page 1
Page 2

then js will send [1,2]. So in livewire, it will this data in the database:

Page 1 → page_number 1
Page 2 → page_number 2

WHEN REORDERED:
Move Page 2 before Page 1


--}}