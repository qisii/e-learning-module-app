@extends('layouts.app')

@section('title', 'Pretest')

@section('header', 'Pretest')

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
        @if(!empty($gdriveLink))
            <a href="{{ $gdriveLink }}" 
            target="_blank" 
            title="Download from Google Drive"
            class="px-6 py-4 text-[12px] rounded-xl bg-purple-100 text-purple-800 border border-purple-200 shadow-sm 
                    hover:bg-purple-200 hover:shadow-md transition-all duration-200 flex items-center gap-2 font-secondary">
            
                <i class="ri-external-link-line text-[14px]"></i> 
                Download from Google Drive
            </a>
        @else
            <span class="px-6 py-4 text-[12px] rounded-xl bg-gray-100 text-gray-400 border border-gray-200 
                        font-semibold flex items-center gap-2 transition-all duration-200 font-secondary">
                <i class="ri-error-warning-line text-[14px]"></i>
                No Google Drive link available
            </span>
        @endif
    </div>

    @include('components.new-components.back-confirmation-user-modal')

    {{-- Pretest Quiz --}}
    <div class="w-[90%] lg:w-[80%] mx-auto">
        @if (empty($pretest))
            <livewire:student-pretest-quiz-form" :project="$project"/>
        @else
            <livewire:student-pretest-quiz-form :project="$project" :quiz="$pretest"/>
        @endif
    </div>
</div>
@endsection
