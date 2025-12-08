@extends('layouts.app')

@section('title', 'Module')

@section('header', 'Module')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar">

    {{-- Go Back + Tabs Container --}}
    <div class="flex items-center justify-between mb-6">

        {{-- Back Button --}}
        <button type="button" 
                onclick="openBackModal()" 
                class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
                style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
        </button>

        @include('components.new-components.back-confirmation-modal')

        {{-- Tabs Section --}}
        <div class="flex items-center gap-2 bg-gray-100 rounded-xl">
            <a href="{{ route('admin.module.show', $folder->id) . '?level_id=' . $level_id }}"
                class="px-6 py-4 text-[12px] rounded-xl text-[#6B7280] hover:text-[#374151] transition">
                Form
            </a>

            <a href="{{ route('admin.module.preview', $folder->id) . '?level_id=' . $level_id }}"
                class="px-6 py-4 text-[12px] rounded-xl bg-[#E5E7EB] text-[#374151] font-semibold cursor-default">
                Preview
            </a>
        </div>

    </div>

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
                    The content for this module has not been added yet.<br>
                    You can continue editing or upload content to make it available for preview.
                </p>
            </div>
        @else
            {{-- MAIN WRAPPER --}}
            <div class="relative w-[90%] mx-auto overflow-auto no-scrollbar mt-[4%] px-0 lg:px-5 py-10 rounded-lg bg-white shadow-md">

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
                <div class="my-10 space-y-6 mx-8 overflow-auto">
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

</div>
@endsection
