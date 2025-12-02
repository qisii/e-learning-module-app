@extends('layouts.app')

@section('title','Module Handout')

@section('header', 'Module Handout')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10">
    <button type="button" 
            onclick="openBackModal()" 
            class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
        style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
    </button>

    @include('components.new-components.back-confirmation-user-modal')

    <div class="relative w-[90%] mx-auto mt-[6%] p-20 rounded-lg bg-white shadow-md flex flex-col items-center justify-center">
         <div class="text-center mb-10">
            <h2 class="text-lg font-bold text-gray-800 font-secondary">Module Handout</h2>
            <span class="inline-block mt-2 px-4 py-1 bg-purple-100 text-purple-600 text-sm font-semibold rounded-full font-secondary">
                @if ($level_id == 1) Easy
                @elseif ($level_id == 2) Average
                @elseif ($level_id == 3) Hard
                @else Unknown Level
                @endif
            </span>
        </div>

        <p class="text-gray-500 text-[13px] mb-5" style="font-family: 'Inter', sans-serif;">
            You may access other modules.
        </p>

        {{-- Other Module Handouts Button --}}
        @if ($level_id == 1)
            <a href="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 2]) }}"
            class="inline-flex items-center justify-center text-sm  py-3 mb-3
                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                    text-white rounded-lg transition-all duration-500 
                    ease-in-out w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
            style="font-family: 'Inter', sans-serif;">
                Check Average Module
                <i class="ri-arrow-right-line ml-3"></i>
            </a>
        @elseif ($level_id == 2)
            <a href="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 3]) }}"
            class="inline-flex items-center justify-center text-sm py-3 mb-3
                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                    text-white rounded-lg transition-all duration-500 
                    ease-in-out w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
            style="font-family: 'Inter', sans-serif;">
                Check Hard Module
                <i class="ri-arrow-right-line ml-3"></i>
            </a>
        @endif
        {{-- If level_id == 3 (Hard), no other module button is displayed --}}

        {{-- Post Test Button --}}
        <a href="{{ route('projects.welcome.posttest', $project_id) }}"
        class="inline-flex items-center justify-center text-sm py-3
                bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                text-white rounded-lg transition-all duration-500 
                ease-in-out w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
        style="font-family: 'Inter', sans-serif;">
            Continue to Post Test
            <i class="ri-arrow-right-line ml-3"></i>
        </a>

    </div>
</div>
@endsection
