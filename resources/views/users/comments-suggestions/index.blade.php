@extends('layouts.app')

@section('title', 'Comments & Suggestions')
@section('header', 'Comments & Suggestions')
@section('comment-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full min-h-[80vh] flex items-center justify-center p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar"
     style="font-family: 'Poppins', sans-serif;">

    <div class="bg-white w-full max-w-xl shadow-xl rounded-2xl px-6 py-10 lg:px-10 lg:py-15">

        <form action="{{ route('comments.suggestions.store') }}" method="post" class="w-full" enctype="multipart/form-data">
            @csrf

            {{-- EMOJI + CATCHY MESSAGE --}}
            <div class="mb-6 text-center">
                <div class="text-6xl mb-3 animate-bounce">💬</div>

                <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-2">
                    We want to hear from you!
                </h2>

                <p class="text-gray-500 text-sm">
                    Tell us what you liked, or what we can make better 🌈
                </p>
            </div>

            {{-- TEXTAREA --}}
            <div class="relative mb-3">
                <label for="body"
                    class="block text-[14px] text-gray-600 mb-2 font-medium"
                    style="font-family: 'Inter', sans-serif;">
                    Share your thoughts ✨
                </label>

                <textarea
                    id="body"
                    name="body"
                    rows="5"
                    placeholder="I liked the lesson because..."
                    class="w-full p-3 bg-white border border-gray-200 text-[14px] rounded-lg resize-none shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                        transition-all duration-200 ease-in-out placeholder:text-gray-400
                        @error('body') border-red-500 focus:ring-red-300 @enderror"
                    style="font-family: 'Inter', sans-serif;"
                    maxlength="300"
                    oninput="updateCounter()"
                ></textarea>

                @error('body')
                    <span class="absolute left-0 top-full mb-1 text-red-500 text-[10px]">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- HELPER TEXT + COUNTER --}}
            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-400 text-[12px]">
                    Help us understand and support you 💙
                </p>

                <p id="charCount" class="text-gray-400 text-[12px]">
                    0 / 300
                </p>
            </div>

            {{-- SUBMIT BUTTON --}}
            <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-emerald-400 to-emerald-600
                    text-white text-[14px] font-semibold rounded-lg
                    hover:from-emerald-500 hover:to-emerald-700
                    transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg
                    active:scale-95">
                Send my message 🚀
            </button>

        </form>
    </div>
</div>

{{-- SIMPLE JS --}}
<script>
    function updateCounter() {
        const textarea = document.getElementById('body');
        const counter = document.getElementById('charCount');
        counter.textContent = `${textarea.value.length} / 300`;
    }
</script>
@endsection
