<div class="border border-gray-200 rounded-lg p-4 shadow-sm bg-gray-50">
    <div class="mb-3">
        <label class="block text-gray-600 mb-1 font-medium" style="font-family: 'Inter', sans-serif;">
            Question {{ $index + 1 }}
        </label>
        <textarea
            wire:model="questions.{{ $index }}.question_text"
            placeholder="Enter question..."
            rows="2"
            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 resize-none text-[12px]"
            style="font-family: 'Inter', sans-serif;"
        ></textarea>
    </div>

    {{-- Choices --}}
    <div class="space-y-2 mb-3">
        @foreach ($questions[$index]['choices'] as $cIndex => $choice)
            @include('livewire.components.choice', ['qIndex' => $index, 'cIndex' => $cIndex])
        @endforeach
    </div>

    {{-- Add Choice --}}
    <button type="button" 
        wire:click="addChoice({{ $index }})"
        class="flex items-center text-blue-600 text-[12px] hover:underline">
        <i class="ri-add-line mr-1"></i> Add Choice
    </button>
</div>
