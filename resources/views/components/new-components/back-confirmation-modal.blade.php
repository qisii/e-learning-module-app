<!-- 🧩 Confirmation Modal -->
<div id="backModal" 
     class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50 transition-opacity duration-300">

    <div id="backModalContent"
         class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 w-80 md:w-[420px]
                transform scale-90 opacity-0 transition-all duration-300 ease-out relative font-primary">

        <!-- Icon + Title -->
        <div class="flex items-start gap-4">
            <div class="bg-red-100 rounded-full px-4 py-3">
                <i class="ri-delete-bin-6-line text-red-600 text-xl"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Unsaved Data?
                </h2>
                <p class="text-gray-600 text-sm mt-1 leading-relaxed">
                    The data you entered has not been saved and will be lost if you continue. 
                    This action cannot be undone.
                </p>
            </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex justify-end gap-2 mt-6 font-secondary">
            <!-- Cancel -->
            <button type="button"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 text-sm"
                onclick="closeBackModal()">
                Cancel
            </button>

            <!-- Continue -->
            <form action="{{ route('admin.projects') }}" method="GET" 
                  onsubmit="clearSessionBeforeLeaving()">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                    Continue
                </button>
            </form>
        </div>

        <!-- Close Icon -->
        <button type="button" 
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition"
                onclick="closeBackModal()">
            <i class="ri-close-line text-xl"></i>
        </button>

    </div>
</div>

{{-- JS --}}
<script>
function openBackModal() {
    const modal = document.getElementById('backModal');
    const content = document.getElementById('backModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-90', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeBackModal() {
    const modal = document.getElementById('backModal');
    const content = document.getElementById('backModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-90', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>
