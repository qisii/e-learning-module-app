@extends('layouts.app')

@section('title', 'Projects')

@section('header', 'Projects')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10">

    <div class="w-full" style="font-family: 'Poppins', sans-serif;">
        <h2 class="text-xl font-semibold text-gray-800 mb-10">
            All Projects
        </h2>

        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @forelse ($all_projects as $project)
                @php
                    $colors = [
                        ['bg' => 'bg-blue-400', 'tab' => 'bg-blue-300', 'icon' => 'ri-book-2-fill', 'icon-color' => 'text-blue-700'],
                        ['bg' => 'bg-pink-400', 'tab' => 'bg-pink-300', 'icon' => 'ri-book-2-fill', 'icon-color' => 'text-pink-700'],
                        ['bg' => 'bg-purple-400', 'tab' => 'bg-purple-300', 'icon' => 'ri-book-2-fill', 'icon-color' => 'text-purple-700'],
                        ['bg' => 'bg-teal-400', 'tab' => 'bg-teal-300', 'icon' => 'ri-book-2-fill', 'icon-color' => 'text-teal-700'],
                        ['bg' => 'bg-orange-400', 'tab' => 'bg-orange-300', 'icon' => 'ri-book-2-fill', 'icon-color' => 'text-orange-700'],
                    ];
                    $style = $colors[$loop->index % count($colors)];
                @endphp

                <!-- Clickable Folder-like Project Card -->
                <a href="{{ route('projects.welcome.pretest', $project->id) }}" class="block group">
                    <div class="relative w-full h-full select-none text-[#F9FAFB]">
                        <!-- Folder tab -->
                        <div class="absolute -top-3 left-1 w-20 h-5 rounded-t-md rounded-b-sm {{ $style['tab'] }} shadow-sm -skew-x-6 
                                    transition-all duration-300 group-hover:translate-y-[-2px]"></div>

                        <!-- Folder body -->
                        <div 
                            class="{{ $style['bg'] }} text-white rounded-md p-5 flex flex-col justify-between shadow-md 
                                hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 hover:scale-[1.02] relative w-full h-full cursor-pointer"
                        >
                            <!-- Icon Section -->
                            <div class="flex justify-between items-start">
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <i class="{{ $style['icon'] }} text-3xl {{ $style['icon-color'] }} 
                                    transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></i>
                                </div>
                            </div>

                            <!-- Text Section -->
                            <div class="mt-2">
                                <p class="font-semibold text-lg mb-2 text-gray-50 group-hover:text-white">
                                    {{ $project->title }}
                                </p>
                                <p class="text-[13px] leading-snug mb-5 text-gray-100" style="font-family: 'Inter', sans-serif">
                                    {{ $project->description }}
                                </p>
                                <p class="text-[12px] text-gray-200" style="font-family: 'Inter', sans-serif">
                                    <span class="font-semibold">Created:</span> {{ date('d M Y', strtotime($project->created_at)) }}
                                    &nbsp;|&nbsp;
                                    <span class="font-semibold">Updated:</span> {{ date('d M Y', strtotime($project->updated_at)) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-gray-500 py-6 col-span-3">
                    No projects yet.
                </div>
            @endforelse
        </div>


        <div class="mt-8">
            {{ $all_projects->links() }}
        </div>
    </div>
</div>
@endsection
