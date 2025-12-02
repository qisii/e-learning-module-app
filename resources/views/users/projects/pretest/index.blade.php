@extends('layouts.app')

@section('title', $project->title)

@section('header', $project->title)

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

    @if ($pretestFolder && $pretestFolder->quizzes->first()?->questions->isNotEmpty())
    {{-- Pretest exists and has questions --}}
        <div class="w-[90%] lg:w-[80%] mx-auto mt-[35%] lg:mt-[10%] flex flex-col items-center justify-center text-center">
            <div class="text-7xl mb-6 animate-bounce">🎯</div>

            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                Welcome on board!
            </h1>

            <p class="text-gray-600 text-lg mb-8" style="font-family: 'Inter', sans-serif;">
                Ready to take the quiz and test your knowledge?
            </p>

            <a 
                href="{{ route('projects.show.pretest', $project->id) }}"
                class="inline-flex items-center justify-center px-8 sm:px-10 py-3 sm:py-4 
                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                    text-white text-base sm:text-lg rounded-lg transition-all duration-500 
                    ease-in-out w-fit mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
                style="font-family: 'Inter', sans-serif;"
            >
                Start Quiz
                <i class="ri-arrow-right-line ml-3"></i>
            </a>
        </div>
    @else
        {{-- No pretest or no questions --}}
        <div class="w-[90%] lg:w-[80%] mx-auto mt-[35%] lg:mt-[10%] flex flex-col items-center justify-center text-center">
            <div class="text-7xl mb-6 animate-bounce">📝</div>

            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                Almost ready!
            </h1>

            <p class="text-gray-600 text-lg mb-8" style="font-family: 'Inter', sans-serif;">
                The pretest is on its way. Please check back soon once the quiz has been added.
            </p>
        </div>
    @endif

</div>
@endsection
