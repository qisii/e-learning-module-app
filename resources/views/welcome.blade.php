@extends('layouts.home')

@section('title', 'Home')

@section('content')
    {{-- banner --}}
    <div class="w-[90%] md:w-[85%] lg:w-[85%] min-h-[75vh] flex flex-col lg:flex-row my-4 mx-auto items-center justify-center lg:justify-between gap-6" id="home">
        {{-- Left side: Text --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center text-center lg:text-left">
            <div 
                class="bg-[#DBEAFE] text-[#1E40AF] uppercase inline-block px-4 py-2 rounded-md mb-4 
                    text-xs sm:text-sm font-semibold tracking-wider mx-auto lg:mx-0"
                style="font-family: 'Inter', sans-serif;"
            >
                Lorem ipsum dolor sit amet consectetur
            </div>

            <h1 
                class="text-[#111827] font-bold text-[22px] sm:text-[26px] lg:text-[36px] leading-tight mb-4"
                style="font-family: 'Poppins', sans-serif;"
            >
                Lorem ipsum dolor sit amet consectetur 
                <span class="text-[#3B82F6]">adipisicing elit.</span>
            </h1>

            <p 
                class="text-[#6B7280] text-[14px] sm:text-[16px] lg:text-[20px] mb-8"
                style="font-family: 'Inter', sans-serif;"
            >
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nobis exercitationem est, eligendi velit suscipit minus!
            </p>

            <a 
                href="{{ route('register') }}"
                class="inline-flex items-center justify-center px-8 sm:px-10 py-3 sm:py-4 
                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                    text-white text-base sm:text-lg rounded-lg transition-all duration-500 
                    ease-in-out w-fit mx-auto lg:mx-0 hover:bg-right"
                style="font-family: 'Inter', sans-serif;"
            >
                Get Started
                <i class="ri-arrow-right-line ml-3"></i>
            </a>
        </div>

        {{-- Right side: Image --}}
        <div class="w-full lg:w-1/2 flex justify-center lg:justify-end mt-8 lg:mt-0">
            <div class="flex items-center justify-center w-[80%] sm:w-[70%] lg:w-[80%]">
                <img 
                    src="{{ asset('assets/images/logo-md2.png') }}" 
                    alt="Logo" 
                    class="object-contain w-[70%] sm:w-[80%] lg:w-[90%]"
                >
            </div>
        </div>
    </div>

    {{-- About Us --}}
    <div class="w-[90%] md:w-[85%] lg:w-[85%] min-h-[80vh] md:min-h-[65vh] flex mt-10 lg:mt-[10%] my-4 mx-auto" id="about">
        <div class="w-full mt-auto">
            <div class="text-center my-8">
                <p class="uppercase text-[#1E40AF] font-bold text-sm" style="font-family: 'Inter', sans-serif;">
                    About Us
                </p>
                <h3 class="font-bold text-[#111827] text-[22px] md:text-[20px] my-6" style="font-family: 'Poppins', sans-serif;">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Asperiores, voluptate.
                </h3>
            </div>

            {{-- About Us --}}
            <div class="flex flex-col md:flex-row justify-center items-stretch gap-6 h-auto md:h-[75%]">
                <div class="flex-1 bg-[#F9FAFB] rounded-lg shadow-sm p-8 lg:p-12 text-center">
                    <div class="w-18 h-18 bg-[#DBEAFE] rounded-full mx-auto flex items-center justify-center">
                        <i class="ri-lightbulb-line text-[#3B82F6] text-[40px] md:text-[35px]"></i>
                    </div>
                    <p class="font-bold text-[16px] md:text-[14px] text-[#111827] text-center my-3" style="font-family: 'Poppins', sans-serif;">
                        Lorem ipsum dolor sit amet.
                    </p>
                    <p class="text-justify text-[14px] md:text-[13px] text-[#6B7280]" style="font-family: 'Inter', sans-serif;">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Magni sapiente sit, alias incidunt nesciunt exercitationem similique! Provident placeat quia eum?
                    </p>
                </div>

                <div class="flex-1 bg-[#F9FAFB] rounded-lg shadow-sm p-8 lg:p-12 text-center">
                    <div class="w-18 h-18 bg-[#DBEAFE] rounded-full mx-auto flex items-center justify-center">
                        <i class="ri-arrow-right-up-line text-[#3B82F6] text-[40px] md:text-[35px]"></i>
                    </div>
                    <p class="font-bold text-[16px] md:text-[14px] text-[#111827] text-center my-3" style="font-family: 'Poppins', sans-serif;">
                        Lorem ipsum dolor sit amet.
                    </p>
                    <p class="text-justify text-[14px] md:text-[13px] text-[#6B7280]" style="font-family: 'Inter', sans-serif;">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Magni sapiente sit, alias incidunt nesciunt exercitationem similique! Provident placeat quia eum?
                    </p>
                </div>

                <div class="flex-1 bg-[#F9FAFB] rounded-lg shadow-sm p-8 lg:p-12 text-center">
                    <div class="w-18 h-18 bg-[#DBEAFE] rounded-full mx-auto flex items-center justify-center">
                        <i class="ri-book-shelf-line text-[#3B82F6] text-[40px] md:text-[35px]"></i>
                    </div>
                    <p class="font-bold text-[16px] md:text-[14px] text-[#111827] text-center my-3" style="font-family: 'Poppins', sans-serif;">
                        Lorem ipsum dolor sit amet.
                    </p>
                    <p class="text-justify text-[14px] md:text-[13px] text-[#6B7280]" style="font-family: 'Inter', sans-serif;">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Magni sapiente sit, alias incidunt nesciunt exercitationem similique! Provident placeat quia eum?
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="w-full lg:min-h-[60vh] md:min-h-[45vh] sm:min-h-[55vh] bg-[#0D1D46] mt-50 text-center relative overflow-hidden">
        {{-- Decorative Images --}}
        <img src="{{ asset('assets/images/periwrinkle.png') }}" 
            alt="Fame Decoration" 
            class="absolute top-[30%] left-[20%] opacity-20 w-[150px] pointer-events-none select-none">
            
        <img src="{{ asset('assets/images/Tube.png') }}" 
            alt="Fame Decoration" 
            class="absolute bottom-0 right-[80%] opacity-20 w-[120px] pointer-events-none select-none">

        <img src="{{ asset('assets/images/Tube.png') }}" 
            alt="Fame Decoration" 
            class="absolute bottom-0 right-0 opacity-20 w-[600px] pointer-events-none select-none">

        {{-- Content --}}
        <div class="w-[70%] mx-auto lg:py-[14%] md:py-[15%] py-[30%] relative z-10">
            <p class="text-[36px] md:text-[32px] lg:text-[50px] font-bold text-[#F9FAFB] mb-4 leading-tight" 
                style="font-family: 'Poppins', sans-serif;">
                Ready to take the quiz?
            </p>

            <p class="w-[70%] md:w-[85%] sm:w-full mx-auto text-center text-[12px] md:text-[14px] lg:text-[16px] text-[#E5E7EB] mb-6 leading-relaxed" 
                style="font-family: 'Inter', sans-serif;">
                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nulla dolore consectetur eos perferendis ad dicta fugit, ducimus hic unde alias dignissimos cum aliquid vero aut! Error exercitationem ullam molestias officia.
            </p>

            <a href="{{ route('register') }}"
                class="inline-flex items-center px-10 py-4 md:px-8 md:py-3 sm:px-6 sm:py-2 bg-gradient-to-r from-blue-500 to-blue-900 
                    bg-[length:150%_150%] bg-left rounded-lg text-white text-lg md:text-[15px] sm:text-[13px] transition-all 
                    duration-500 ease-in-out w-fit hover:bg-right"
                style="font-family: 'Inter', sans-serif;">
                Get Started
                <i class="ri-arrow-right-line ml-3"></i>
            </a>
        </div>
    </div>
@endsection