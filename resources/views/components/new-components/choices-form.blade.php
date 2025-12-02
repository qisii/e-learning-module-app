<div class="flex items-center gap-2">
    <input type="radio" 
        wire:model="questions.{{ $qIndex }}.correct_choice" 
        value="{{ $cIndex }}"
        class="text-blue-500 focus:ring-blue-400">
    <input type="text"
        wire:model="questions.{{ $qIndex }}.choices.{{ $cIndex }}.choice_text"
        placeholder="Enter choice text..."
        class="flex-1 p-2 border border-gray-300 rounded-lg text-[12px] focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
        style="font-family: 'Inter', sans-serif;">
    <button type="button" wire:click="removeChoice({{ $qIndex }}, {{ $cIndex }})" 
        class="text-gray-400 hover:text-red-500">
        <i class="ri-delete-bin-line"></i>
    </button>
</div>
