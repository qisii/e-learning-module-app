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
            class="px-6 py-4 text-[10px] lg:text-[12px] rounded-xl text-[#6B7280] hover:text-[#374151] transition">
            Pretest
        </a>

        <a href="{{ route('admin.grades.posttest') }}"
            class="px-6 py-4 text-[10px] lg:text-[12px] rounded-xl text-[#6B7280] hover:text-[#374151] transition">
            Post-test
        </a>

        <a href="{{ route('admin.grades.module') }}"
            class="px-6 py-4 text-[10px] lg:text-[12px] rounded-xl bg-[#E5E7EB] text-[#374151] font-semibold cursor-default">
            Module
        </a>
    </div>

    {{-- Module Content --}}
    <div class="bg-white rounded-xl shadow-md p-6 text-[12px] overflow-auto">

        {{-- Filter Input --}}
        <div class="flex flex-col sm:flex-row w-full sm:items-center justify-end gap-3 lg:gap-4">
            <form action="{{ route('admin.grades.module.search') }}" method="get" class="flex flex-col sm:flex-row w-full sm:items-center gap-3 lg:gap-4">

                {{-- Search --}}
                <div class="relative w-full sm:w-[50%] lg:w-[20%]">
                    <input 
                        id="search"
                        type="search" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search here..." 
                        class="border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:outline-none 
                            focus:ring-2 focus:ring-blue-500 w-full"
                    >
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                {{-- Date --}}
                <div class="w-full sm:w-[50%] lg:w-[20%]">
                    <input 
                        type="date" 
                        name="date"
                        value="{{ request('date') }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none 
                            focus:ring-2 focus:ring-blue-500 text-[12px] w-full"
                    >
                </div>

                {{-- Submit Button (optional) --}}
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-[12px] hover:bg-blue-700 transition">
                    Filter
                </button>

            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto mt-5">
            <table class="min-w-full border-collapse table-auto">
                <thead class="bg-[#f0f4ff] text-gray-700 uppercase">
                    <tr>
                        <th class="py-4 px-6 text-left font-semibold">Student Name</th>
                        <th class="py-4 px-6 text-left font-semibold">Username</th>
                        <th class="py-4 px-6 text-left font-semibold">Grade</th>
                        <th class="py-4 px-6 text-left font-semibold">Section</th>
                        <th class="py-4 px-6 text-left font-semibold">Project Title</th>
                        <th class="py-4 px-6 text-left font-semibold">Level</th>
                        <th class="py-4 px-6 text-left font-semibold">Time Spent</th>
                        <th class="py-4 px-6 text-left font-semibold">Attempts <span class="lowercase">(th)</span></th>
                        <th class="py-4 px-6 text-left font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @if(count($modules) > 0)
                        @foreach($modules as $attempt)
                            <tr class="hover:bg-[#f9fbff] transition">
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $attempt->user->first_name ?? 'N/A' }} {{ $attempt->user->last_name ?? '' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $attempt->user->username ?? '-' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $attempt->user->grade_level ?? '-' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $attempt->user->section ?? '-' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $attempt->handout->folder->project->title ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    @if ($attempt->handout->level_id == 1)
                                        <span class="text-green-500 font-semibold">Easy</span>
                                    @elseif ($attempt->handout->level_id == 2)
                                        <span class="text-yellow-500 font-semibold">Average</span>
                                    @elseif ($attempt->handout->level_id == 3)
                                        <span class="text-red-500 font-semibold">Hard</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">Unknown Level</span>
                                    @endif
                                </td>

                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ gmdate('i:s', $attempt->time_spent) }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100">
                                    {{ $attempt->attempt_number }}
                                </td>
                                <td class="py-4 px-6 border-b border-gray-100 text-gray-500">
                                    {{ $attempt->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center p-3">No module attempts yet.</td>
                        </tr>
                    @endif
                </tbody>

            </table>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $modules->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
