@extends('layouts.auth')

@section('title', 'Login')

@section('image')
    <!-- Desktop: large login image at center-left -->
    <img src="{{ asset('assets/images/login2.png') }}" 
        alt="Login" 
        class="hidden lg:block absolute bottom-[-15%] left-[26%] transform -translate-x-1/2 w-[400px] z-0">

    <!-- Tablet & Mobile: smaller login image bottom-left -->
    <img src="{{ asset('assets/images/login2.png') }}" 
        alt="Login" 
        class="md:w-[220px] md:block lg:hidden absolute bottom-[-17%] left-[4%] hidden z-0">
@endsection

@section('content')
<div class="w-full flex flex-col lg:flex-row min-h-[80vh] md:h-auto relative">
    <!-- Left Side Images -->
    <div class="w-full lg:w-1/2 flex justify-center items-center relative order-1 lg:order-none">
        <div class="w-[90%] h-full rounded-lg p-6 relative overflow-hidden flex justify-center">
            
            <img src="{{ asset('assets/images/stair.png') }}" alt="Stair" 
                class="hidden lg:block absolute top-[10%] left-[10%] w-[120px] z-[-20]">
            <img src="{{ asset('assets/images/periwrinkle.png') }}" alt="Periwinkle" 
                class="hidden lg:block absolute top-[20%] left-[70%] w-[120px]">
            <img src="{{ asset('assets/images/Star.png') }}" alt="Star" 
                class="hidden lg:block absolute top-[70%] right-[10%] w-[80px]">
            <img src="{{ asset('assets/images/Star.png') }}" alt="Star" 
                class="hidden lg:block absolute top-[85%] right-[10%] w-[20px]">
            <img src="{{ asset('assets/images/Tube.png') }}" alt="Tube" 
                class="hidden lg:block absolute top-[85%] right-[75%] w-[80px]">
        </div>
    </div>
    {{-- Tablet & Mobile Layout --}}
    <img src="{{ asset('assets/images/stair.png') }}" alt="Stair" 
        class="block lg:hidden absolute top-[5%] left-[15%] w-[70px] md:w-[90px] opacity-90 z-[-20]">
    <img src="{{ asset('assets/images/periwrinkle.png') }}" alt="Periwinkle" 
        class="block lg:hidden absolute top-[10%] right-[12%] w-[90px] md:w-[110px] opacity-90">
    <img src="{{ asset('assets/images/star.png') }}" alt="Star" 
        class="block lg:hidden absolute bottom-[18%] right-[20%] w-[50px] md:w-[100px] opacity-80 z-[-20]">
    <img src="{{ asset('assets/images/periwrinkle.png') }}" alt="Tube" 
        class="block lg:hidden absolute bottom-[5%] md:bottom-0 md:left-[50%] left-[10%] w-[100px] md:w-[130px] opacity-80">


    <!-- Login Form -->
    <div class="w-full lg:w-1/2 flex justify-center items-center mt-2 md:mt-4 lg:mt-0">
        <div class="w-[90%] h-full rounded-lg p-6 pt-0">
            <div x-data="{ role: 'student' }" class="mt-0 md:mt-10 px-4 md:px-16 lg:px-24 w-full">
                
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <h2 class="text-[24px] md:text-[28px] font-bold text-[#111827] text-center mb-8 md:mb-10 font-secondary" >
                    Login
                </h2>

                <!-- Tabs -->
                <div class="flex justify-center mb-1 space-x-10 font-secondary">
                    <button type="button"
                        class="text-[16px] font-semibold pb-2 transition-all duration-200 border-b-3"
                        :class="role === 'student' ? 'border-[#1E40AF] text-[#1E40AF]' : 'border-transparent text-[#6B7280]'"
                        @click="role = 'student'">
                        Student
                    </button>
                    <button type="button"
                        class="text-[16px] font-semibold pb-2 transition-all duration-200 border-b-3"
                        :class="role === 'teacher' ? 'border-[#1E40AF] text-[#1E40AF]' : 'border-transparent text-[#6B7280]'"
                        @click="role = 'teacher'">
                        Teacher
                    </button>
                </div>

                {{-- You already fixed validation separation; now you just need to make sure the correct form stays open after redirect with errors. --}}
                <div class="relative overflow-hidden mb-0 h-[372px] md:h-[350px] lg:h-[373px]">
                    
                    <!-- STUDENT FORM -->
                    <form 
                        x-show="role === 'student'" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        action="{{ route('login') }}" 
                        method="POST"
                        class="absolute inset-0 flex-col justify-center px-2 pt-6"
                    >
                        @csrf

                       <div class="relative mb-5">
                            <input 
                                id="username" 
                                type="text" 
                                name="username" 
                                value="{{ old('username') }}"
                                placeholder="Username"
                                class="w-full p-4 bg-[#F3F4F6] text-[14px] rounded-md font-secondary focus:outline-none focus:ring-2 focus:ring-blue-500 
                                    @error('username') border border-red-500 @enderror"
                                
                                autofocus
                            >

                            @error('username')
                                <span 
                                    class="absolute left-0 top-full mt-1 text-red-500 font-secondary text-[11px] whitespace-nowrap"
                                    >
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>


                        <div class="relative mb-10">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                value="{{ old('password') }}"
                                placeholder="Password"
                                class="w-full p-4 bg-[#F3F4F6] text-[14px] rounded-md font-secondary focus:outline-none focus:ring-2 focus:ring-blue-500 
                                    @error('password') border border-red-500 @enderror"
                                  
                                
                            >

                            @error('password')
                                <span 
                                    class="absolute left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap font-secondary"
                                    >
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <input type="hidden" name="role_id" value="2">

                        <div class="">
                            <button type="submit"
                                class="w-full py-4 text-[14px] bg-[#1E40AF] text-[#F9FAFB] font-semibold rounded-md hover:bg-blue-900 transition duration-200 font-secondary"
                                >
                                Login
                            </button>
                        </div>
                    </form>

                    <!-- TEACHER FORM -->
                    <form 
                        x-show="role === 'teacher'" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        action="{{ route('login') }}" 
                        method="POST"
                        class="absolute inset-0 flex-col justify-center px-2 pt-6"
                    >
                        @csrf

                        <div class="relative mb-5">
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                placeholder="Email Address"
                                class="w-full p-4 bg-[#F3F4F6] text-[14px] font-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 
                                    @error('email') border border-red-500 @enderror"
                                  
                                autofocus
                            >

                            @error('email')
                                <span 
                                    class="absolute left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap font-secondary"
                                    >
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="relative mb-10">
                            <input 
                                id="teacher-password" 
                                type="password" 
                                name="teacher_password" 
                                value="{{ old('teacher_password') }}"
                                placeholder="Password"
                                class="w-full p-4 bg-[#F3F4F6] text-[14px] rounded-md font-secondary focus:outline-none focus:ring-2 focus:ring-blue-500 
                                    @error('teacher_password') border border-red-500 @enderror"
                            >

                            @error('teacher_password')
                                <span 
                                    class="absolute left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap font-secondary">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <input type="hidden" name="role_id" value="1">

                        <div class="">
                            <button type="submit"
                                class="w-full py-4 text-[14px] bg-[#1E40AF] text-[#F9FAFB] font-semibold rounded-md hover:bg-blue-900 transition duration-200 font-secondary">
                                Login
                            </button>
                        </div>

                        <!-- Login with Google (Teacher only) -->
                        <div class="mt-4 flex justify-center">
                            <div class="w-10 h-10 bg-[#DBEAFE] rounded-full flex items-center justify-center">
                                <a href="{{ route('auth.google') }}">
                                    <i class="ri-google-fill text-[#3B82F6] text-[20px]"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                </div>

                <p class="text-[14px] text-[#6B7280] text-center mt-7 font-secondary">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-[#1E40AF] font-bold">Signup</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
