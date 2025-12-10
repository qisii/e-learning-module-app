<nav 
    x-data="{ open: false }"
    class="bg-white shadow-sm py-6 relative z-50"
>
    <div class="w-[90%] md:w-[85%] mx-auto flex items-center justify-between">
        {{-- Left side: Logo --}}
        <div class="flex items-center">
            <h1 
                class="text-lg sm:text-xl lg:text-2xl font-bold text-blue-900 truncate max-w-[200px] sm:max-w-none"
                style="font-family: 'Poppins', sans-serif;"
            >
                {{ config('app.name') }}
            </h1>
        </div>

        {{-- Desktop Links --}}
        @if (Route::has('login'))
            <div 
                class="hidden md:flex space-x-4 items-center" 
                style="font-family: 'Inter', sans-serif;"
                x-data="{ active: '#home' }"
            >
                <a 
                    href="#home" 
                    @click.prevent="active = '#home'; window.location.hash = 'home'" 
                    :class="active === '#home' ? 'text-blue-700 font-semibold' : 'text-gray-700'"
                    class="hover:text-blue-700 transition-colors duration-200"
                >
                    Home
                </a>

                <a 
                    href="#about" 
                    @click.prevent="active = '#about'; window.location.hash = 'about'" 
                    :class="active === '#about' ? 'text-blue-700 font-semibold' : 'text-gray-700'"
                    class="hover:text-blue-700 transition-colors duration-200"
                >
                    About
                </a>

                @auth
                    @if(Auth::user()->role_id === 1)
                        <a href="{{ url('admin/profile/show') }}"
                            class="px-6 py-3 border border-blue-800 text-blue-800 rounded-lg hover:bg-blue-50 transition duration-200">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ url('/profile/show') }}"
                            class="px-6 py-3 border border-blue-800 text-blue-800 rounded-lg hover:bg-blue-50 transition duration-200">
                            Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                        class="px-6 py-3 border border-blue-800 text-blue-800 rounded-lg hover:bg-blue-50 transition duration-200">
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="px-6 py-3 border border-blue-500 bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left text-white rounded-lg transition-all duration-500 ease-in-out hover:bg-right">
                            Get Started
                            <i class="ri-arrow-right-line ml-3"></i>
                        </a>
                    @endif
                @endauth
            </div>
        @endif


        {{-- Burger Menu Icon for Tablet/Mobile --}}
        <button 
            @click="open = !open"
            class="md:hidden text-2xl text-gray-800 focus:outline-none"
        >
            <i class="ri-menu-line"></i>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div 
        x-show="open"
        x-transition
        class="absolute top-full left-0 w-full bg-white shadow-md md:hidden"
        style="font-family: 'Inter', sans-serif;"
        x-data="{ active: '#home' }"
    >
        <div class="flex flex-col items-start space-y-2 py-4 px-6">
            <a 
                href="#home"
                @click.prevent="active = '#home'; window.location.hash = 'home'; open = false;"
                :class="active === '#home' ? 'text-blue-700 font-semibold' : 'text-gray-700'"
                class="w-full text-left hover:text-blue-700 transition-colors duration-200 py-2"
            >
                Home
            </a>

            <a 
                href="#about"
                @click.prevent="active = '#about'; window.location.hash = 'about'; open = false;"
                :class="active === '#about' ? 'text-blue-700 font-semibold' : 'text-gray-700'"
                class="w-full text-left hover:text-blue-700 transition-colors duration-200 py-2"
            >
                About
            </a>

            {{-- Divider --}}
            <div class="w-full border-t border-gray-200 my-1"></div>

            @auth
                <a href="{{ url('/profile/show') }}"
                    class="w-full text-left text-blue-800 hover:text-blue-700 transition duration-200 py-2">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="w-full text-left text-blue-700 font-normal hover:text-blue-900 transition duration-200 py-2">
                    Login
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="w-full text-left text-blue-700 font-semibold hover:text-blue-900 transition duration-200 py-2 flex items-center">
                        Get Started
                        <i class="ri-arrow-right-line ml-2 text-[18px]"></i>
                    </a>
                @endif
            @endauth
        </div>
    </div>

</nav>
