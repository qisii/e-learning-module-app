@if (!empty($question['id']))
<!-- Delete Question Modal -->
<div id="deleteModal-{{ $question['id'] }}" 
        class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50 transition-opacity duration-300">
    <div id="deleteModalContent-{{ $question['id'] }}" 
            class="bg-gradient-to-br from-red-100 to-red-50 border border-red-300 rounded-xl shadow-md p-6 w-80 md:w-96 
                transform scale-90 opacity-0 transition-all duration-300 ease-out relative">
        
        <div class="flex items-center gap-3 mb-4">
            <i class="ri-error-warning-fill text-red-600 text-lg"></i>
            <h2 class="text-lg font-semibold text-red-800" style="font-family: 'Inter', sans-serif;">
                Confirm Delete
            </h2>
        </div>

        <p class="text-red-700 text-sm mb-6" style="font-family: 'Inter', sans-serif;">
            Are you sure you want to delete this question? <br> This action cannot be undone.
        </p>

        <div class="flex justify-end gap-2" style="font-family: 'Poppins', sans-serif;">
            <!-- Cancel -->
            <button type="button" 
                    class="px-4 py-2 bg-red-100 text-red-800 rounded hover:bg-red-200 transition text-[12px]"
                    onclick="closeDeleteModal({{ $question['id'] }})">
                Cancel
            </button>
            
            <!-- Confirm Delete -->
            <form action="{{ route('admin.questions.delete', $question['id']) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-[12px]">
                    Delete
                </button>
            </form>
        </div>

        <!-- Close Icon -->
        <button type="button" 
                class="absolute top-3 right-3 text-red-500 hover:text-red-700 transition"
                onclick="closeDeleteModal({{ $question['id'] }})">
            <i class="ri-close-line text-xl"></i>
        </button>
    </div>
</div>
@endif


<script>
function openDeleteModal(id) {
    const modal = document.getElementById('deleteModal-' + id);
    const content = document.getElementById('deleteModalContent-' + id);

    modal.classList.remove('hidden');

    setTimeout(() => {
        content.classList.remove('scale-90', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeDeleteModal(id) {
    const modal = document.getElementById('deleteModal-' + id);
    const content = document.getElementById('deleteModalContent-' + id);

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-90', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>
