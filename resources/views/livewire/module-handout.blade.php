<div>
    <div class="w-full mx-auto grid grid-cols-1 lg:grid-cols-7 lg:gap-6">
        {{-- Left palette --}}
        <div class="bg-white rounded-lg shadow-md p-4 mb-5 lg:col-span-1 w-full sticky top-5 z-10 font-secondary self-start">
            <h3 class="text-sm font-semibold text-gray-500 mb-2">
                Components
            </h3>

            {{-- Add Page button --}}
            <div class="my-4">
                <p class="text-gray-500 mb-2 font-medium text-[11px]">Clickable</p>
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
                <p class="text-gray-500 mb-2 font-medium text-[11px]">Drag & Drop to page</p>
                <div class="palette-item flex items-center py-3 px-3 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 cursor-grab" data-type="text">
                    <i class="ri-text align-middle text-gray-500 text-lg mr-3"></i>
                    <span class="text-gray-700 font-medium text-[11px]">Editor</span>
                </div>

                <div class="palette-item flex items-center py-3 px-3 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 cursor-grab" data-type="objective">
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
                    <li>
                        Resize images/videos to set a fixed height for consistent display.
                    </li>
                </ul>
            </div>

            {{-- Score Area --}}
            <div class="bg-white shadow-lg rounded-lg px-5 py-3 mb-6 font-secondary">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-gray-700 text-[12px]">
                            This module handout will display if the student's pretest score is
                        </span>
                        <input 
                            type="number"
                            wire:model.defer="handoutScore"
                            class="w-20 rounded-md font-bold border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2 text-[12px]"
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

            {{-- PDF gdrive --}}
            <div class="bg-white shadow-lg rounded-lg px-5 py-3 mb-6 font-secondary">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-gray-700 text-[12px]">
                            Enter a Google Drive link to allow students to download the module PDF.
                        </span>
                        <input 
                            type="text"
                            wire:model.defer="gdriveLink"
                            class="w-full rounded-md border border-gray-300 p-2 text-[12px] focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="https://drive.google.com/file/d/xxxx"
                        >
                    </div>

                    <div wire:click="saveGDrivePdf" class="hover:text-green-700 text-green-500 ms-auto text-[13px] cursor-pointer">
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
                        @endif
                    </span>
                </div>

                {{-- Components Section --}}
                <div class="pages-sortable space-y-4 overflow-auto font-secondary text-sm">
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
                            <div class="components-list overflow-auto" data-page-id="{{ $page->id }}">
                                @foreach ($page->components as $component)
                                    <div class="component-block border border-gray-200 p-3 rounded bg-white flex items-start gap-3 overflow-auto"
                                        data-component-id="{{ $component->id }}" wire:key="handout-component-{{ $component->id }}">

                                        {{-- Component content --}}
                                        <div class="flex-1 font-medium text-gray-700 overflow-auto">

                                            {{-- TEXT BLOCK --}}
                                            @if ($component->type === 'text')
                                                <div class="flex justify-between items-center mb-2 overflow-auto">

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
                                                        data-editor-type="main"
                                                    >{{ optional(json_decode($component->data, true))['content'] ?? '' }}</textarea>
                                                </div>
                                            {{-- HIDDEN OBJECTIVE BLOCK --}}
                                            @elseif ($component->type === 'objective')
                                                <div class="flex justify-between items-center mb-2 overflow-auto">

                                                    {{-- LABEL --}}
                                                    <div class="text-[13px] text-gray-700">
                                                        Hidden Objective
                                                    </div>

                                                    {{-- SAVE BUTTON --}}
                                                    <div class="hover:text-green-700 text-green-500 ms-auto text-[13px]">
                                                        <button
                                                            type="button"
                                                            onclick="saveObjectiveWithTargets({{ $component->id }})"
                                                            class="cursor-pointer"
                                                        >
                                                            <i class="ri-checkbox-circle-line"></i> Save
                                                        </button>
                                                    </div>

                                                    {{-- DELETE BUTTON --}}
                                                    <button wire:click.prevent="removeComponent({{ $component->id }})"
                                                            class="text-red-500 hover:text-red-700">
                                                        <i class="ri-delete-bin-line text-sm ms-2"></i>
                                                    </button>
                                                </div>

                                                {{-- OBJECTIVE INPUTS --}}
                                                <div class="space-y-3 p-3 border rounded-md bg-gray-50">
                                                    {{-- Target Selection --}}
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Target Element(s)</label>

                                                        <div class="flex items-center gap-2 mb-2">
                                                            {{-- Select Editor --}}
                                                            <button
                                                                type="button"
                                                                class="px-3 py-1 text-xs bg-gray-500 hover:bg-gray-600 text-white rounded select-editor-btn"
                                                                data-objective-id="{{ $component->id }}"
                                                            >
                                                                Select Editor
                                                            </button>

                                                            {{-- Add Target --}}
                                                            <button 
                                                                type="button"
                                                                class="px-3 py-1 text-xs bg-blue-500 text-white rounded select-target-btn opacity-50 cursor-not-allowed"
                                                                data-objective-id="{{ $component->id }}"
                                                                disabled
                                                            >
                                                                + Add Target
                                                            </button>

                                                            {{-- Reminder --}}
                                                            <span class="text-[11px] text-gray-400">
                                                                (Please select text only · Max 2 targets)
                                                            </span>
                                                        </div>

                                                        {{-- TARGET LIST --}}
                                                        <div
                                                            class="target-list grid grid-cols-2 gap-2 mt-2"
                                                            data-objective-id="{{ $component->id }}"
                                                        >
                                                        </div>
                                                    </div>

                                                    {{-- Display Message --}}
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Instruction</label>
                                                        <textarea 
                                                            class="w-full border rounded p-2 text-sm"
                                                            rows="1"
                                                            wire:model.defer="objectiveData.{{ $component->id }}.instruction"
                                                        ></textarea>
                                                    </div>

                                                    {{-- Completion Message --}}
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Completion Message</label>
                                                        <textarea 
                                                            class="w-full border rounded p-2 text-sm"
                                                            rows="2"
                                                            wire:model.defer="objectiveData.{{ $component->id }}.completion_message"
                                                        ></textarea>
                                                    </div>
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
    <!-- Custom alert container -->
    <div id="custom-alert" class="fixed inset-0 flex items-center justify-center bg-black/50 hidden z-50 font-secondary">
        <div class="bg-white p-5 rounded-lg shadow-lg w-[360px] h-[200px] flex flex-col justify-between text-center">
            
            <p id="custom-alert-message"
            class="text-gray-800 overflow-y-auto">
            </p>

            <button id="custom-alert-ok"
                    class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                OK
            </button>
        </div>
    </div>

</div>
