<div class="bg-white shadow-lg rounded-lg p-5">
    {{-- QUIZ HEADER --}}
    <div class="text-center mb-2">
        {{-- Folder Type --}}
        <h2 class="text-lg font-bold text-gray-800" style="font-family: 'Poppins', sans-serif;">
            @if ($folder->folder_type_id == 1)
                Pretest Quiz
            @elseif ($folder->folder_type_id == 3)
                Post-test Quiz
            @else
                Quiz
            @endif
        </h2>
    </div>

    <form wire:submit="store" class="text-[12px]" enctype="multipart/form-data">
        {{-- INSTRUCTIONS --}}
        <div class="relative mb-5">
            <label for="instructions" 
                class="block text-gray-600 mb-2 font-medium"
                style="font-family: 'Inter', sans-serif;">
                Instructions
            </label>

            <textarea 
                id="instructions" 
                name="instructions"
                wire:model="instructions"
                rows="2"
                placeholder="Please write the instructions..."
                class="w-full p-3 bg-white border border-gray-200 rounded-lg resize-none shadow-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                    transition-all duration-200 ease-in-out placeholder:text-gray-400
                    @error('instructions') border border-red-500 focus:ring-red-300 @enderror"
                style="font-family: 'Inter', sans-serif;"
            ></textarea>

            @error('instructions')
                <span 
                    class="absolute left-0 top-full text-red-500 text-[11px] whitespace-nowrap"
                    style="font-family: 'Poppins', sans-serif;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- QUESTIONS --}}
        <div class="space-y-5 mb-5">
            @foreach ($questions as $qIndex => $question)
                <div class="border border-gray-200 rounded-lg p-4 shadow-sm bg-gray-50 relative">
                    {{-- Question Header (label left + delete button right) --}}
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-gray-700" style="font-family: 'Inter', sans-serif;">
                            {{ $question['label'] }}
                        </p>

                        <button 
                            type="button" 
                            wire:click="deleteQuestion({{ $qIndex }})"
                            class="text-gray-400 hover:text-red-500 transition"
                            title="Delete question"
                        >
                            <i class="ri-delete-bin-fill text-lg"></i>
                        </button>
                    </div>

                    {{-- Question Textarea --}}
                    <div class="relative mb-3">
                        <textarea 
                            name="question_text" 
                            wire:model.defer="questions.{{ $qIndex }}.text"
                            rows="1"
                            placeholder="Write your question here..."
                            class="w-full p-3 bg-white border border-gray-300 rounded-lg resize-none shadow-sm
                                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                                transition-all duration-200 ease-in-out placeholder:text-gray-400 text-[12px]
                                @error('questions.' . $qIndex . '.text') border border-red-500 focus:ring-red-300 @enderror"
                            style="font-family: 'Inter', sans-serif;"
                        ></textarea>

                        @error('questions.' . $qIndex . '.text')
                            <span 
                                class="absolute left-0 top-full text-red-500 text-[11px] whitespace-nowrap"
                                style="font-family: 'Poppins', sans-serif;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Choices --}}
                    @if (!empty($question['choices']))
                        <div class="mt-4 space-y-2">
                            <p class="font-semibold text-gray-700 mb-2 text-start">Choices</p>

                            @foreach ($question['choices'] as $cIndex => $choice)
                                <div class="relative flex items-center space-x-2">
                                    <input 
                                        type="radio" 
                                        wire:model="questions.{{ $qIndex }}.correct_choice"
                                        value="{{ $cIndex }}"
                                        class="text-blue-500 focus:ring-blue-400"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model.defer="questions.{{ $qIndex }}.choices.{{ $cIndex }}.text"
                                        placeholder="Choice text"
                                        class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none text-[12px]
                                            @error('questions.' . $qIndex . '.choices.' . $cIndex . '.text') border border-red-500 focus:ring-red-300 @enderror"
                                        style="font-family: 'Inter', sans-serif;"
                                    >

                                    {{-- Delete Choice Button --}}
                                    <button 
                                        type="button"
                                        wire:click="deleteChoice({{ $qIndex }}, {{ $cIndex }})"
                                        class="text-gray-400 hover:text-red-500 transition"
                                        title="Delete choice"
                                    >
                                        <i class="ri-delete-bin-fill text-lg"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Add Choice Button --}}
                    @if (count($question['choices']) < 4)
                        <div class="mt-4 flex justify-start">
                            <button 
                                type="button"
                                wire:click="addChoice({{ $qIndex }})"
                                class="w-1/4 lg:w-1/6 px-1 lg:px-3 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600 transition"
                                style="font-family: 'Inter', sans-serif;"
                            >
                                Add choice
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ADD QUESTION BUTTON --}}
        <div class="flex items-center justify-center mb-5">
            <button 
                type="button" 
                wire:click="addQuestion"
                class="flex items-center justify-center w-full p-1 border-2 border-dashed border-blue-400 rounded-lg hover:bg-blue-50 transition"
            >
                <i class="ri-add-line text-blue-500 text-lg mr-1"></i>
                <span class="text-blue-600 font-medium" style="font-family: 'Inter', sans-serif;">
                    Add question
                </span>
            </button>
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
            class="w-full lg:w-1/3 ms-auto py-2 lg:mb-0 bg-[#10B981] text-[#F9FAFB] text-[14px] font-medium rounded-md 
                hover:bg-[#065F46] transition duration-200 block cursor-pointer"
            style="font-family: 'Inter', sans-serif;">
            Save
        </button>
    </form>
</div>
