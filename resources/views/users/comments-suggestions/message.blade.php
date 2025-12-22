@extends('layouts.app')

@section('title', 'Comments & Suggestions')
@section('header', 'Comments & Suggestions')
@section('comment-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full min-h-[80vh] flex items-center justify-center p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar"
     style="font-family: 'Poppins', sans-serif;">

    <div class="bg-white w-full max-w-xl shadow-xl rounded-2xl px-6 py-10 lg:px-10 lg:py-15 text-center">

        {{-- EMOJI WITH ANIMATION --}}
        <div class="text-7xl mb-4 animate-bounce">
            🎉
        </div>

        {{-- THANK YOU MESSAGE --}}
        <h2 class="text-2xl lg:text-3xl font-extrabold text-gray-800 mb-3">
            Thank you for sharing!
        </h2>

        <p class="text-gray-600 text-sm lg:text-base mb-8 leading-relaxed">
            Your comments and suggestions really matter to us 💙  
            You’re helping make learning more fun and better for everyone!
        </p>

        {{-- Friendly follow-up question --}}
        <p class="text-gray-500 text-sm mb-4">
            Got more ideas or something else to share? 😊
        </p>

        {{-- BUTTON (ANCHOR TAG) --}}
        <a href="{{ route('comments.suggestions.index') }}"
        class="inline-flex items-center justify-center px-6 py-3
                bg-gradient-to-r from-blue-500 to-blue-700
                text-white text-sm font-semibold rounded-xl
                hover:from-blue-600 hover:to-blue-800
                transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg
                active:scale-95">

            Share more thoughts 🌟
        </a>

    </div>
</div>
@endsection
