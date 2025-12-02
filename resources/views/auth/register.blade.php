@extends('layouts.auth')

@section('title', 'Signup')

@section('image')
<!-- Desktop: large login image at center-left -->
    <img src="{{ asset('assets/images/signup2.png') }}" 
        alt="Login" 
        class="hidden lg:block absolute bottom-[-15%] left-[26%] transform -translate-x-1/2 w-[400px] z-15">

    <!-- Tablet & Mobile: smaller login image bottom-left -->
    <img src="{{ asset('assets/images/signup2.png') }}" 
        alt="Login" 
        class="md:w-[220px] md:block lg:hidden absolute bottom-[-17%] left-[4%] hidden z-15">
@endsection

@section('content')
    <div class="w-full flex flex-col lg:flex-row min-h-[80vh] md:h-auto relative">
        <div class="w-full lg:w-1/2 flex justify-center items-center relative order-1 lg:order-none">
            <div class="w-[90%] h-full rounded-lg p-6 relative overflow-hidden flex justify-center">
                <img src="{{ asset('assets/images/star.png') }}" alt="Star" 
                    class="hidden lg:block absolute top-[10%] left-[10%] w-[120px]">
                <img src="{{ asset('assets/images/stair.png') }}" alt="Periwinkle" 
                    class="hidden lg:block absolute top-[20%] left-[70%] w-[120px] ">
                <img src="{{ asset('assets/images/tube.png') }}" alt="Tube" 
                    class="hidden lg:block absolute top-[70%] right-[10%] w-[80px]">
                <img src="{{ asset('assets/images/star.png') }}" alt="Star" 
                    class="hidden lg:block absolute top-[85%] right-[10%] w-[20px]">
                <img src="{{ asset('assets/images/periwrinkle.png') }}" alt="Periwinkle" 
                    class="hidden lg:block absolute top-[85%] right-[75%] w-[80px]">
            </div>
        </div>
        {{-- Tablet & Mobile Layout --}}
        <img src="{{ asset('assets/images/star.png') }}" alt="Star" 
            class="block lg:hidden absolute top-[10%] left-[10%] md:w-[120px]  w-[60px]">
        <img src="{{ asset('assets/images/stair.png') }}" alt="Periwinkle" 
            class="block lg:hidden absolute top-[10%] left-[70%] md:left-[82%] w-[60px] ">
        <img src="{{ asset('assets/images/tube.png') }}" alt="Tube" 
            class="block lg:hidden absolute top-[70%] right-[10%] w-[80px]">
        <img src="{{ asset('assets/images/periwrinkle.png') }}" alt="Star" 
            class="block lg:hidden absolute top-[85%] right-[10%] w-[100px]">
        <img src="{{ asset('assets/images/star.png') }}" alt="Periwinkle" 
            class="block lg:hidden absolute top-[95%] right-[50%] w-[50px]">
        
        {{-- Register Form --}}
        <div class="w-full lg:w-1/2 flex justify-center items-center mt-2 md:mt-4 lg:mt-0 z-15">
        <div class="w-[90%] h-full rounded-lg p-6 pt-0">
            <div x-data="{ role: 'student' }" class="mt-0 md:mt-10 px-4 md:px-16 lg:px-24 w-full">
                    
                    <h2 class="text-[24px] md:text-[28px] font-bold text-[#111827] text-center mb-8 md:mb-10 font-secondary" 
                        >
                        Create an account
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

                    <div class="relative overflow-hidden mb-0 h-[372px] lg:h-[373px] md:h-[370px]">
                        
                        <!-- STUDENT FORM -->
                        <form 
                            x-show="role === 'student'" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            action="{{ route('register') }}" 
                            method="POST"
                            class="absolute inset-0 flex-col justify-center px-2 pt-6"
                        >
                            @csrf

                            <div class="mb-5 flex gap-4">
                                <div class="relative w-1/2">
                                    <input 
                                        id="student-first-name"
                                        type="text" 
                                        name="student_first_name" 
                                        value="{{ old('student_first_name') }}"
                                        placeholder="First Name"
                                        class="w-full p-4 bg-[#F3F4F6] text-[14px] font-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                            @error('student_first_name') border border-red-500 @enderror"
                                        
                                        autofocus
                                    >

                                    @error('student_first_name')
                                        <span 
                                            class="absolute font-secondary left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                            >
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="relative w-1/2">
                                    <input 
                                        id="student-last-name"
                                        type="text" 
                                        name="student_last_name" 
                                        value="{{ old('student_last_name') }}"
                                        placeholder="Last Name"
                                        class="w-full p-4 bg-[#F3F4F6] font-secondary text-[14px] rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                            @error('student_last_name') border border-red-500 @enderror"
                                        
                                        
                                    >

                                    @error('student_last_name')
                                        <span 
                                            class="absolute font-secondary left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                            >
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="relative mb-5">
                                <input 
                                    id="username" 
                                    type="text" 
                                    name="username" 
                                    value="{{ old('username') }}"
                                    placeholder="Username"
                                    class="w-full p-4 bg-[#F3F4F6] text-[14px] font-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        @error('username') border border-red-500 @enderror"
                                    
                                    
                                >

                                @error('username')
                                    <span 
                                        class="absolute font-secondary left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                        >
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="relative mb-10">
                                <input 
                                    id="student-password" 
                                    type="password" 
                                    name="student_password" 
                                    value="{{ old('student_password') }}"
                                    placeholder="Password"
                                    class="w-full p-4 bg-[#F3F4F6] font-secondary text-[14px] rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        @error('student_password') border border-red-500 @enderror"
                                     
                                    
                                >

                                @error('student_password')
                                    <span 
                                        class="absolute left-0 font-secondary top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                        >
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <input type="hidden" name="role_id" value="2">

                            <div class="px-1">
                                <button type="submit"
                                    class="w-full py-4 font-secondary text-[14px] bg-[#1E40AF] text-[#F9FAFB] font-semibold rounded hover:bg-blue-900 transition duration-200"
                                    >
                                    Signup
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
                            action="{{ route('register') }}" 
                            method="POST"
                            class="absolute inset-0 flex flex-col justify-center px-2 pt-6"
                        >
                            @csrf

                            <div class="mb-5 flex gap-4">
                                <div class="relative w-1/2">
                                    <input 
                                        id="teacher-first-name"
                                        type="text" 
                                        name="teacher_first_name" 
                                        value="{{ old('teacher_first_name') }}"
                                        placeholder="First Name"
                                        class="w-full p-4 bg-[#F3F4F6] font-secondary text-[14px] rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                            @error('teacher_first_name') border border-red-500 @enderror"
                                        
                                        autofocus
                                    >

                                    @error('teacher_first_name')
                                        <span 
                                            class="absolute font-secondary left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                            >
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="relative w-1/2">
                                    <input 
                                        id="teacher-last-name"
                                        type="text" 
                                        name="teacher_last_name" 
                                        value="{{ old('teacher_last_name') }}"
                                        placeholder="Last Name"
                                        class="w-full p-4 bg-[#F3F4F6] font-secondary text-[14px] rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                            @error('teacher_last_name') border border-red-500 @enderror"
                                        
                                        
                                    >

                                    @error('teacher_last_name')
                                        <span 
                                            class="absolute font-secondary left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                            >
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="relative mb-5">
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    placeholder="Email Address"
                                    class="w-full p-4 bg-[#F3F4F6] text-[14px] font-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        @error('email') border border-red-500 @enderror"
                                    
                                    
                                >

                                @error('email')
                                    <span 
                                        class="absolute left-0 font-secondary top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
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
                                    class="w-full p-4 bg-[#F3F4F6] text-[14px] font-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        @error('teacher_password') border border-red-500 @enderror"
                                      
                                    
                                >

                                @error('teacher_password')
                                    <span 
                                        class="absolute left-0 top-full font-secondary mt-1 text-red-500 text-[11px] whitespace-nowrap"
                                        >
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <input type="hidden" name="role_id" value="1">

                            <div class="px-1">
                                <button type="submit"
                                    class="w-full py-4 font-secondary text-[14px] bg-[#1E40AF] text-[#F9FAFB] font-semibold rounded hover:bg-blue-900 transition duration-200"
                                    >
                                    Signup
                                </button>
                            </div>

                            <!-- Google icon (Teacher only) -->
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
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-[#1E40AF] font-bold">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection


