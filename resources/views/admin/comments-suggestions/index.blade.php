@extends('layouts.app')

@section('title', 'Comments & Suggestions')
@section('header', 'Comments & Suggestions')
@section('comment-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar"
     style="font-family: 'Poppins', sans-serif;">

    {{-- HEADER TEXT --}}
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-1">
            Explore students’ thoughts 💬
        </h2>
        <p class="text-gray-500 text-sm">
            View comments and suggestions shared by students to help improve learning.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 text-[12px] overflow-auto">

        {{-- FILTER INPUT --}}
        <div class="flex flex-col sm:flex-row w-full sm:items-center justify-end gap-3 lg:gap-4 mb-4">
            <form action="{{ route('admin.comments.suggestions.search') }}"
                  method="get"
                  class="flex flex-col sm:flex-row w-full sm:items-center gap-3 lg:gap-4">

                {{-- SEARCH --}}
                <div class="relative w-full sm:w-[50%] lg:w-[25%]">
                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name, grade, section..."
                        class="border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 w-full"
                    >
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                {{-- DATE --}}
                <div class="w-full sm:w-[50%] lg:w-[20%]">
                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 text-[12px] w-full"
                    >
                </div>

                {{-- FILTER BUTTON --}}
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-[12px]
                               hover:bg-blue-700 transition">
                    Filter
                </button>

            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto mt-5">
            <table class="min-w-full border-collapse table-auto">
                <thead class="bg-[#f0f4ff] text-gray-700 uppercase">
                    <tr>
                        <th class="py-4 px-6 text-left font-semibold">Student Name</th>
                        <th class="py-4 px-6 text-left font-semibold">Username</th>
                        <th class="py-4 px-6 text-left font-semibold">Grade</th>
                        <th class="py-4 px-6 text-left font-semibold">Section</th>
                        <th class="py-4 px-6 text-left font-semibold">Date</th>
                    </tr>
                </thead>

                <tbody class="text-gray-600">
                    @forelse($comments as $comment)
                        <tr
                            class="hover:bg-[#f9fbff] transition cursor-pointer"
                            onclick="openModal(this)"
                            data-name="{{ $comment->user->first_name ?? '' }} {{ $comment->user->last_name ?? '' }}"
                            data-grade="{{ $comment->user->grade ?? 'N/A' }}"
                            data-section="{{ $comment->user->section ?? 'N/A' }}"
                            data-date="{{ $comment->created_at->format('M d, Y') }}"
                            data-body="{{ e($comment->body) }}"
                        >

                            <td class="py-4 px-6 border-b border-gray-100 font-medium">
                                {{ $comment->user->first_name ?? 'N/A' }}
                                {{ $comment->user->last_name ?? '' }}
                            </td>

                            <td class="py-4 px-6 border-b border-gray-100">
                                {{ $comment->user->username ?? '-' }}
                            </td>

                            <td class="py-4 px-6 border-b border-gray-100">
                                {{ $comment->user->grade ?? '-' }}
                            </td>

                            <td class="py-4 px-6 border-b border-gray-100">
                                {{ $comment->user->section ?? '-' }}
                            </td>

                            <td class="py-4 px-6 border-b border-gray-100 text-gray-500">
                                {{ $comment->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-6 text-gray-500">
                                No comments or suggestions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION --}}
            <div class="mt-8">
                {{ $comments->links() }}
            </div>

            {{-- MODAL --}}
           <div id="commentModal"
                class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-3 hidden">

                <div id="modalContent"
                    class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6
                            transform scale-95 opacity-0 transition-all duration-300 relative">

                    {{-- CLOSE BUTTON --}}
                    <button onclick="closeModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl leading-none">
                        &times;
                    </button>

                    {{-- HEADER --}}
                    <div class="mb-4">
                        <h3 id="modalName" class="text-lg font-bold text-gray-800 mb-3"></h3>

                        {{-- META INFO (VERTICAL, FULL WIDTH) --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 shadow-sm text-xs text-gray-700 space-y-1 w-full">
                            <p id="modalGrade"></p>
                            <p id="modalSection"></p>
                            <p id="modalDate"></p>
                        </div>
                    </div>

                    {{-- BODY (WHITE BOX, FIXED HEIGHT) --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm text-[13px] text-gray-700 leading-relaxed
                                h-48 overflow-y-auto mt-4">
                        <p id="modalBody" class="whitespace-pre-line"></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    const modal = document.getElementById('commentModal');
    const modalContent = document.getElementById('modalContent');

    function openModal(row) {
        document.getElementById('modalName').textContent = row.dataset.name;
        document.getElementById('modalGrade').textContent = `Grade: ${row.dataset.grade}`;
        document.getElementById('modalSection').textContent = `Section: ${row.dataset.section}`;
        document.getElementById('modalDate').textContent = `Date: ${row.dataset.date}`;
        document.getElementById('modalBody').textContent = row.dataset.body;

        modal.classList.remove('hidden');

        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Close on background click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>

</div>
@endsection
