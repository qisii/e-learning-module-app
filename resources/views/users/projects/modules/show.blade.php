@extends('layouts.app')

@section('title','Module Handout')
@section('header', 'Module Handout')
@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10">
    <div class="flex items-center justify-between mb-6">
        {{-- Go Back Button --}}
        <button type="button" 
                onclick="openBackModal()" 
                class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
                style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
        </button>

        {{-- Download Button --}}
        <a href="#" 
        class="px-6 py-4 text-[12px] rounded-xl bg-gray-200 text-[#374151] hover:bg-gray-300 font-semibold transition">
            <i class="ri-arrow-down-line me-2 text-[13px]"></i> Download
        </a>
    </div>

    @include('components.new-components.back-confirmation-user-modal')

        {{-- CHECK IF MODULE HAS CONTENT --}}
        @php
            $hasContent = $pages->count() > 0 && $pages->first()->components->count() > 0;
        @endphp

        {{-- IF NO CONTENT – SHOW "MODULE ON ITS WAY" --}}
        @if (!$hasContent)
            <div class="w-[90%] lg:w-[80%] mx-auto mt-[20%] lg:mt-[10%] flex flex-col items-center justify-center text-center">
                <div class="text-7xl mb-6 animate-bounce">📘</div>

                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                    Almost ready!
                </h1>

                <p class="text-gray-600 text-lg mb-8" style="font-family: 'Inter', sans-serif;">
                    The module is on its way. Please check back soon once the content has been added.
                </p>
            </div>
        @else
            {{-- MAIN WRAPPER --}}
            <div class="relative w-[90%] mx-auto max-h-[1056px] overflow-auto no-scrollbar mt-[4%] px-10 py-15 rounded-lg bg-white shadow-md">

                {{-- Title Header --}}
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-gray-800 font-secondary">Module Handout</h2>
                    <span class="inline-block mt-2 px-4 py-1 bg-purple-100 text-purple-600 text-sm font-semibold rounded-full font-secondary">
                        @if ($level_id == 1) Easy
                        @elseif ($level_id == 2) Average
                        @elseif ($level_id == 3) Hard
                        @else Unknown Level
                        @endif
                    </span>
                </div>

                {{-- HANDOUT CONTENT --}}
                <div class="my-10 space-y-6 mx-8">
                    @foreach ($pages as $page)
                        @foreach ($page->components as $component)
                            @php
                                $data = json_decode($component->data, true);
                                $html = $data['content'] ?? '';
                            @endphp

                            <div class="prose max-w-none">
                                {!! $html !!}
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif

        </div> {{-- END WHITE BOX --}}

    {{-- PAGINATION ALWAYS OUTSIDE WHITE CONTAINER --}}
    @if ($hasContent)
        <div class="w-[90%] mx-auto mt-8">
            {{ $pages->links() }}
        </div>
    @endif


    {{-- BOTTOM BUTTONS — ONLY ON LAST PAGE AND ONLY IF CONTENT EXISTS --}}
    @if ($hasContent && $pages->onLastPage())

        <p class="text-gray-500 text-[13px] text-center mt-10" style="font-family: 'Inter', sans-serif;">
            You may access other modules.
        </p>

        <div class="flex flex-col items-center mt-4">

            {{-- Other Module buttons --}}
            @if ($level_id == 1)
                <a href="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 2]) }}"
                    class="inline-flex items-center justify-center text-sm py-3 mb-3
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
    @endif

</div>
@endsection
