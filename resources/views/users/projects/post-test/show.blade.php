@extends('layouts.app')

@section('title', 'Post-test')

@section('header', 'Post-test')

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

    {{-- Post Quiz --}}
    <div class="w-[90%] lg:w-[80%] mx-auto">
        @if (empty($post_test))
            <livewire:student-post-test-quiz-form" :project="$project"/>
        @else
            <livewire:student-post-test-quiz-form :project="$project" :quiz="$post_test"/>
        @endif
    </div>
</div>
@endsection
