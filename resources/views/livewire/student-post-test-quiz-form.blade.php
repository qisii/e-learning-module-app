<div class="max-w-6xl mx-auto flex gap-6">
    {{-- Quiz Form --}}
    <div class="flex-1 bg-white shadow-lg rounded-xl p-5 lg:p-10 bg-cover bg-center overflow-auto no-scrollbar" style="background-image: url('{{ $bgImage }}');">
        {{-- QUIZ HEADER --}}
        <div class="text-center mb-10">
            <h1 class="text-2xl font-extrabold text-gray-800 mb-2" style="font-family: 'Poppins', sans-serif;">
                {{ $project->title }}
            </h1>

            <h2 class="text-[12px] font-semibold text-blue-600 uppercase tracking-widest" style="font-family: 'Inter', sans-serif;">
                Post-test Quiz
            </h2>
            <div class="w-20 mx-auto mt-3 border-b-4 border-blue-500 rounded-full"></div>
        </div>

        {{-- POST TEST QUIZ FORM --}}
        <form wire:submit.prevent="checkAnswers" class="space-y-10 text-[12px]">
            {{-- INSTRUCTIONS --}}
            <div class="mb-5 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start space-x-3">
                <div class="text-blue-500 text-lg">📘</div>
                <div>
                    <h3 class="font-semibold text-blue-700 text-[13px]" style="font-family: 'Poppins', sans-serif;">
                        Instructions
                    </h3>
                    <p class="text-gray-700 text-[12px] leading-relaxed mt-1" style="font-family: 'Inter', sans-serif;">
                        {{ $quiz->instructions }}
                    </p>
                </div>
            </div>

            @foreach ($quiz->questions as $index => $question)
                <div 
                    class="bg-gray-50 p-4 mb-5 rounded-lg border border-gray-200 shadow-sm question-block"
                    style="font-family: 'Poppins', sans-serif;"
                    data-question-id="{{ $question->id }}"
                >
                    {{-- Question Text --}}
                    <div class="mb-2">
                        <h3 class="font-semibold text-gray-800">
                            {{ $index + 1 }}. {{ $question->question_text }}
                        </h3>
                    </div>

                    {{-- Choices --}}
                    <div class="flex flex-wrap gap-5">
                        @foreach ($question->choices as $choice)
                            <label 
                                class="flex items-center space-x-2 cursor-pointer px-3 py-2 rounded-md border transition-all duration-200 ease-in-out transform
                                    hover:scale-[1.03] hover:bg-blue-50 hover:border-blue-300 choice-label
                                    {{ (isset($answers[$question->id]) && $answers[$question->id] == $choice->choice_label_id) ? 'bg-blue-100 border-blue-400 text-blue-700 shadow-sm' : 'bg-white border-gray-200 text-gray-700' }}"
                            >
                                <input 
                                    type="radio" 
                                    name="question_{{ $question->id }}"
                                    value="{{ $choice->choice_label_id }}" 
                                    wire:model="answers.{{ $question->id }}"
                                    wire:click="selectChoice({{ $question->id }}, {{ $choice->choice_label_id }})"
                                    class="text-blue-600 focus:ring-blue-500 choice-input"
                                >
                                <span>
                                    <span class="font-semibold">{{ $choice->label }}.</span> {{ $choice->choice_text }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Validation Error --}}
                    @error('answers.' . $question->id)
                        <span 
                            class="block mt-1 text-red-500 text-[11px]"
                            style="font-family: 'Poppins', sans-serif;"
                        >
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            @endforeach

            {{-- SUBMIT BUTTON --}}
            <button type="button" wire:click="checkAnswers"
                class="w-full lg:w-1/3 ms-auto py-2 lg:mb-0 bg-[#10B981] text-[#F9FAFB] text-[14px] font-medium rounded-md 
                       hover:bg-[#065F46] transition duration-200 block cursor-pointer"
                style="font-family: 'Inter', sans-serif;">
                Submit
            </button>
        </form>
    </div>

    {{-- Sticky Timer Panel (Outside Quiz Card) --}}
    <div class="w-40 sticky top-17 self-start bg-white shadow-lg rounded-xl p-4">
        <h3 class="font-semibold text-gray-800 mb-2">Timer</h3>
        <div wire:poll.1000ms="tick" class="text-2xl font-bold text-blue-600">
            {{ sprintf('%02d:%02d', floor($seconds / 60), $seconds % 60) }}
        </div>
    </div>
</div>


<script>
document.addEventListener('livewire:navigated', initQuizLabels);
document.addEventListener('DOMContentLoaded', initQuizLabels);

function initQuizLabels() {
    const questionBlocks = document.querySelectorAll('.question-block');

    questionBlocks.forEach(block => {
        const labels = block.querySelectorAll('.choice-label');
        const inputs = block.querySelectorAll('.choice-input');

        inputs.forEach(input => {
            input.addEventListener('change', () => {
                labels.forEach(label => {
                    label.classList.remove('bg-blue-100', 'border-blue-400', 'text-blue-700', 'shadow-sm');
                    label.classList.add('bg-white', 'border-gray-200', 'text-gray-700');
                });

                const selectedLabel = input.closest('.choice-label');
                selectedLabel.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
                selectedLabel.classList.add('bg-blue-100', 'border-blue-400', 'text-blue-700', 'shadow-sm');
            });
        });
    });
}
</script>

