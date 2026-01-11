<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- added --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | @yield('title')</title>

    {{-- tailwindcss --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    {{-- external css --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    {{-- Remix Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css" integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Intro.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @vite([
        'resources/js/app.js',
        'resources/js/module-handout.js'
    ])

    {{-- with Alphine JS --}}
    @livewireStyles
</head>
<body 
    x-data="{ sidebarOpen: false }" 
    class="bg-[#F9FAFB] text-gray-900 h-screen flex m-0 p-0 overflow-auto no-scrollbar"
>
    <!-- Sidebar -->
    @if (Auth::user()->role_id == 1)
        <x-new-components.admin-sidebar />
    @else
        <x-new-components.user-sidebar />
    @endif
    

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-y-auto no-scrollbar">
        <!-- Header -->
        <header class="bg-white shadow-sm py-5 px-4 lg:px-10 md:px-10 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <!-- Burger Button (Visible on Mobile/Tablet only) -->
                <button 
                    @click="sidebarOpen = !sidebarOpen" 
                    class="lg:hidden text-[#0B1A3F] fw-bold"
                >
                {{-- class="lg:hidden bg-[#0B1A3F] text-white p-2 rounded-md focus:outline-none" --}}
                    <i class="ri-menu-line text-xl"></i>
                </button>
                
                <h2 class="text-lg md:text-xl font-semibold text-[#6B7280] font-[Poppins]">
                    @yield('header')
                </h2>
            </div>
        </header>

        <!-- After Header -->
        <livewire:flash-message />

        <x-new-components.flash-message 
            :type="session('type')" 
            :message="session('message')" 
        />

        <!-- Page Content -->
        <div class="flex-1 bg-[#F3F4F6] overflow-auto no-scrollbar">
            @yield('content')

            {{-- USER HELP MODAL --}}
            <div id="help-modal"
                class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">

                <div id="help-modal-box"
                    class="bg-white rounded-xl shadow-2xl
                            w-[90%] max-w-lg
                            max-h-[80vh]
                            flex flex-col
                            p-6 relative">

                    <!-- Close Button -->
                    <button id="help-modal-close"
                            class="absolute top-3 right-3 cursor-pointer
                                text-gray-500 hover:text-gray-700 text-2xl font-bold">
                        &times;
                    </button>

                    <!-- Header -->
                    <h2 class="text-xl font-semibold mb-6 text-gray-800 font-secondary text-center">
                        📘 Website Help Guide
                    </h2>

                    <!-- SCROLLABLE CONTENT -->
                    <div class="flex-1 overflow-auto no-scrollbar pr-1">
                        <div class="space-y-5">

                            <!-- Account -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="ri-user-line text-blue-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">Account</p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Update your profile information. Make sure your
                                        <strong>grade level</strong> and <strong>section</strong>
                                        are correct.
                                    </p>
                                </div>
                            </div>

                            <!-- Projects -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="ri-folder-3-line text-indigo-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">Projects</p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Explore learning modules and take quizzes to test your knowledge.
                                    </p>
                                </div>
                            </div>

                            <!-- Grades -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-green-100 flex items-center justify-center">
                                    <i class="ri-table-line text-green-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">Grades</p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        View your quiz scores.
                                        <span class="text-red-500 font-medium">
                                            Scores are deleted after 7 days.
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-orange-100 flex items-center justify-center">
                                    <i class="ri-chat-1-line text-orange-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">
                                        Comments & Suggestions
                                    </p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Share your feedback, ideas, or suggestions to help improve the website.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 text-right">
                        <button id="help-modal-ok"
                                class="px-4 py-2 cursor-pointer bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Got it!
                        </button>
                    </div>
                </div>
            </div>

            {{-- ADMIN HELP MODAL --}}
            <div id="admin-help-modal"
                class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">

                <div id="admin-help-modal-box"
                    class="bg-white rounded-xl shadow-2xl
                            w-[90%] max-w-lg
                            max-h-[80vh]
                            flex flex-col
                            p-6 relative">

                    <!-- Close -->
                    <button id="admin-help-modal-close"
                            class="absolute top-3 right-3 cursor-pointer
                                text-gray-500 hover:text-gray-700 text-2xl font-bold">
                        &times;
                    </button>

                    <h2 class="text-xl font-semibold mb-6 text-gray-800 font-secondary text-center">
                        🛠️ Admin Help Guide
                    </h2>

                    <!-- SCROLLABLE CONTENT -->
                    <div class="flex-1 overflow-auto no-scrollbar pr-1">
                        <div class="space-y-5">
                            <!-- Account -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="ri-user-line text-blue-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">
                                        Account
                                    </p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Update your profile information.
                                        <br>
                                        <span class="text-gray-500">
                                            For accounts registered using Gmail, please make sure
                                            to update your password.
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Projects -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="ri-folder-3-line text-indigo-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">
                                        Projects
                                    </p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Create and manage learning modules and quizzes
                                        that can be shared with students.
                                    </p>
                                </div>
                            </div>

                            <!-- Grades -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-green-100 flex items-center justify-center">
                                    <i class="ri-table-line text-green-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">
                                        Grades
                                    </p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        View quiz scores and module attempts of students.
                                    </p>
                                </div>
                            </div>

                            <!-- Analysis -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="ri-bar-chart-line text-purple-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">
                                        Analysis
                                    </p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Explore students’ learning growth and performance
                                        using insights from modules and quizzes.
                                    </p>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 min-w-[48px] min-h-[48px]
                                            rounded-lg bg-orange-100 flex items-center justify-center">
                                    <i class="ri-chat-1-line text-orange-600 text-[22px]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 font-secondary">
                                        Comments & Suggestions
                                    </p>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        View feedback, comments, and suggestions submitted
                                        by students.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-right">
                        <button id="admin-help-modal-ok"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition cursor-pointer">
                            Got it!
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>
@livewireScripts
@stack('scripts')
<!-- Intro.js JS -->
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

</body>

</html>