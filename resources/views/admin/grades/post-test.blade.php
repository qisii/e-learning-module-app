@extends('layouts.app')

@section('title', 'Grades')
@section('header', 'Grades')
@section('grades-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar" style="font-family: 'Poppins', sans-serif;">

    <h2 class="text-xl font-semibold text-gray-800 mb-6">
        {{ Auth::user()->gender === 'female' ? '👩‍🏫' : '👨‍🏫' }} {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}
    </h2>

    {{-- Tabs Section --}}
    <div class="flex items-center gap-2 mb-6 bg-gray-100 rounded-xl">
        <a href="{{ route('admin.grades.pretest') }}"
            class="px-6 py-4 text-[12px] rounded-xl text-[#6B7280] hover:text-[#374151] transition">
            Pretest
        </a>

        <a href="{{ route('admin.grades.posttest') }}"
            class="px-6 py-4 text-[12px] rounded-xl bg-[#E5E7EB] text-[#374151] font-semibold cursor-default">
            Post-test
        </a>
    </div>

    {{-- Post Test Content --}}
    <div class="bg-white rounded-xl shadow-md p-6 text-[12px]">

        {{-- Search Input --}}
        <div class="flex items-center space-x-2 mb-4">
            <div class="relative w-[50%] lg:w-[30%]">
                <form action="{{ route('admin.grades.posttest.search') }}" method="get">
                    <input 
                        id="search"
                        type="search" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search here..." 
                        class="border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                    >
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse table-auto">
                <thead class="bg-[#f0f4ff] text-gray-700 uppercase">
                    <tr>
                        <th class="py-4 px-6 text-left font-semibold">Student Name</th>
                        <th class="py-4 px-6 text-left font-semibold">Grade</th>
                        <th class="py-4 px-6 text-left font-semibold">Section</th>
                        <th class="py-4 px-6 text-left font-semibold">Project Title</th>
                        <th class="py-4 px-6 text-left font-semibold">Score</th>
                        <th class="py-4 px-6 text-left font-semibold">Time Spent</th>
                        <th class="py-4 px-6 text-left font-semibold">Attempts <span class="lowercase">(th)</span></th>
                        <th class="py-4 px-6 text-left font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @if(count($posttests) > 0)
                        @foreach($posttests as $grade)
                            <tr class="hover:bg-[#f9fbff] transition">
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $grade->user->first_name ?? 'N/A' }} {{ $grade->user->last_name ?? '' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $grade->user->grade_level ?? '-' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $grade->user->section ?? '-' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $grade->quiz->folder->project->title ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100 text-blue-600 font-medium">
                                    {{ $grade->score }} / {{ $grade->quiz->questions->count() }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ gmdate('i:s', $grade->time_spent) }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $grade->attempt_number }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100 text-gray-500">
                                    {{ $grade->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center p-3">No post test results yet.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $posttests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
