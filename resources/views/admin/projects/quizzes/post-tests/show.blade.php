@extends('layouts.app')

@section('title', 'Post-test Quiz')

@section('header', 'Post-test Quiz')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10">
        {{-- Show modal trigger button --}}
        <button type="button" 
                onclick="openBackModal()" 
                class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
        style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
        </button>

        @include('components.new-components.back-confirmation-modal')
    {{-- @else --}}
        {{-- If no session data, just go back normally --}}
        {{-- <a href="{{ route('admin.projects') }}" 
        class="inline-flex text-[14px] items-center text-blue-600 hover:text-blue-800 transition sticky top-10"
        style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-1"></i>
            <span class="font-medium">Go Back</span>
        </a>
    @endif --}}

    {{-- Quiz Form Section --}}
    <div class="w-[90%] lg:w-[80%] mx-auto">
        <div class="md:col-span-2 bg-[#DBEAFE] border border-blue-300 text-blue-800 rounded-lg py-4 px-5 mb-5">
            <div class="flex items-center">
                <i class="ri-information-fill text-blue-600 text-lg mr-2"></i>
                <h2 class="text-[15px] font-semibold" style="font-family: 'Poppins', sans-serif;">
                    Please Note
                </h2>
            </div>
            <p class="text-[13px] leading-relaxed" style="font-family: 'Inter', sans-serif;">
                The size of this form will change depending on how many questions you add. 
                Don’t worry — for students, the quiz will appear one page at a time, 
                so they’ll only see a few questions per page while taking it.
            </p>
        </div>

        @if (empty($quiz))
            <livewire:post-test-quiz-form :folder="$folder"/>
        @else
            <livewire:post-test-quiz-form :folder="$folder" :quiz="$quiz"/>
        @endif
        
        {{-- pt-6 lg:p-6 md:p-6 mb-6 lg:mb-0 --}}
    </div>
</div>

@endsection
