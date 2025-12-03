<div x-data class="relative">
    <!-- Sidebar -->
    <aside 
        :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
        class="fixed top-0 left-0 w-64 h-full bg-[#0B1A3F] text-white flex flex-col justify-between transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:relative z-40"
    >
        <div class="py-6 px-2">
            <h1 class="text-xl text-center font-semibold mb-8 font-[Poppins]">
                My Dashboard
            </h1>

            <!-- Profile -->
            <div class="flex justify-center mb-8">
                @if (Auth::user()->avatar)
                    <img src="{{ Storage::url('avatars/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->avatar }}" class="w-20 h-20 rounded-full">
                @else
                    <div class="w-20 h-20 rounded-full bg-[#0F2250] flex items-center justify-center overflow-hidden">
                        <i class="ri-image-line text-[#E5E7EB] text-3xl"></i>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <nav class="flex flex-col space-y-2 font-[Inter] text-sm">
                <a href="{{ route('admin.profile.show') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                    text-[#E5E7EB] hover:bg-[#0F2250] hover:text-blue-300 @yield('account-active')">
                    <i class="ri-user-line text-[16px]"></i> Account
                </a>
                <a href="{{ route('admin.projects') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                    text-[#E5E7EB] hover:bg-[#0F2250] hover:text-blue-300 @yield('projects-active')">
                    <i class="ri-folder-3-line text-[16px]"></i> Projects
                </a>
                <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                    text-[#E5E7EB] hover:bg-[#0F2250] hover:text-blue-300 @yield('grade-active')">
                    <i class="ri-table-line text-[16px]"></i> Grades
                </a>
                <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                    text-[#E5E7EB] hover:bg-[#0F2250] hover:text-blue-300 @yield('data-active')">
                    <i class="ri-database-2-line text-[16px]"></i> Data
                </a>
                <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                    text-[#E5E7EB] hover:bg-[#0F2250] hover:text-blue-300 @yield('analysis-active')">
                    <i class="ri-bar-chart-line"></i> Analysis
                </a>
                <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                    text-[#E5E7EB] hover:bg-[#0F2250] hover:text-blue-300 @yield('comment-active')">
                    <i class="ri-chat-1-line text-[16px]"></i> Comments & Suggestions
                </a>
            </nav>
        </div>

        <!-- Logout Section -->
        <div class="py-6 px-2 border-t border-blue-900 font-[Inter] text-sm">
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#0F2250] hover:text-blue-300 transition-all duration-200">
                <i class="ri-question-line"></i> Help
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#0F2250] hover:text-[#EF4444] transition-all duration-200 cursor-pointer">
                @csrf
                <button type="submit" class="cursor-pointer">
                    <i class="ri-arrow-right-circle-line mr-2"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay for small screens -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false" 
        class="fixed inset-0 bg-black/50 z-30 lg:hidden"
    ></div>
</div>

<!-- Burger Button (Tablet & Mobile only) -->
    {{-- <button 
        @click="open = !open" 
        class="lg:hidden fixed top-4 left-4 z-50 bg-[#0B1A3F] text-white p-2 rounded-md focus:outline-none"
    >
        <i class="ri-menu-line text-2xl"></i>
    </button> --}}