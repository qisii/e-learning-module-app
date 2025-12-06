@extends('layouts.app')

@section('title', 'Admin Projects')

@section('header', 'Admin Projects')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-2">
                <i class="ri-information-fill text-blue-500 text-xl mr-2"></i>
                <h2 class="text-xl font-semibold text-gray-800" style="font-family: 'Poppins', sans-serif">
                    Reminder
                </h2>
            </div>
            <p class="text-gray-600 leading-relaxed text-[14px]" style="font-family: 'Inter', sans-serif;">
                Every module will only have <strong>three folders</strong>:
                <span class="text-blue-600 font-semibold">Pretest</span>, 
                <span class="text-blue-600 font-semibold">Module</span>, and 
                <span class="text-blue-600 font-semibold">Post-test</span>.
            </p>
        </div>

        {{-- Add New Project Button --}}
        <div class="flex items-center justify-center">
            <a 
                href="{{ route('admin.projects.create') }}" 
                class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-blue-400 rounded-lg hover:bg-blue-50 transition"
            >
                <i class="ri-add-line text-blue-500 text-3xl mb-1"></i>
                <span class="text-blue-600 font-medium text-sm" style="font-family: 'Inter', sans-serif;">
                    Add New Project
                </span>
            </a>
        </div>
    </div>
    
    {{-- Display all projects created by the Auth user --}}
    <div class="w-full mt-12" style="font-family: 'Poppins', sans-serif;">
        <h2 class="text-2xl font-semibold text-gray-800 mb-10">
            All Projects
        </h2>

        @forelse ($all_projects as $project)
            <div class="mt-12">
                <div class="flex justify-between items-center mb-1">
                    <h3 class="text-xl font-bold text-indigo-700 border-l-4 border-indigo-400 pl-2">
                        {{ $project->title }}
                    </h3>

                    <div class="flex items-center space-x-3 pr-2">
                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="text-blue-500 hover:text-blue-700 transition">
                            <i class="ri-edit-fill text-lg"></i>
                        </a>

                        <!-- Delete Button -->
                        <button type="button" 
                                class="text-red-500 hover:text-red-700 transition" 
                                 onclick="openDeleteModal({{ $project->id }})">
                            <i class="ri-delete-bin-6-fill text-lg"></i>
                        </button>

                    </div>
                    @include('components.new-components.delete-project-modal')
                </div>

                <p class="text-gray-500 text-sm ml-3 mb-5" style="font-family: 'Inter', sans-serif;">
                    {{ $project->description }}
                </p>

                {{-- Folder Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 ml-3">
                    @forelse ($project->folders as $folder)
                        @php
                            if ($folder->folder_type_id == 1) {
                                $bg = 'bg-blue-100 text-blue-600';
                                $tab = 'bg-blue-200';
                                $icon = 'ri-lightbulb-flash-fill';
                                $label = 'Pretest';
                            } elseif ($folder->folder_type_id == 2) {
                                $bg = 'bg-pink-100 text-pink-600';
                                $tab = 'bg-pink-200';
                                $icon = 'ri-book-2-fill';
                                $label = 'Module';
                            } elseif ($folder->folder_type_id == 3) {
                                $bg = 'bg-purple-100 text-purple-600';
                                $tab = 'bg-purple-200';
                                $icon = 'ri-check-double-fill';
                                $label = 'Post-test';
                            } else {
                                $bg = 'bg-gray-100 text-gray-600';
                                $tab = 'bg-gray-200';
                                $icon = 'ri-file-2-fill';
                                $label = 'Unknown';
                            }
                        @endphp

                        <!-- Folder-like Project Card -->
                        <div class="relative w-full h-full select-none group">
                            <!-- Folder tab -->
                            <div class="absolute -top-3 left-1 w-20 h-5 rounded-t-md rounded-b-sm {{ $tab }} shadow-sm -skew-x-6 
                                        transition-all duration-300 group-hover:translate-y-[-2px]"></div>

                            <!-- Folder body -->
                            <div 
                                x-data="{ open: false }" 
                                class="{{ $bg }} rounded-md p-4 flex flex-col justify-between shadow-sm 
                                    hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 hover:scale-[1.02] 
                                    relative w-full h-full"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="w-10 h-10 flex items-center justify-center">
                                        <i class="{{ $icon }} text-3xl transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></i>
                                    </div>
                                    
                                    <!-- More Icon + Dropdown -->
                                    <div class="relative">
                                        <i 
                                            @click="open = !open" 
                                            class="ri-more-fill text-gray-500 cursor-pointer"
                                        ></i>

                                        <div 
                                            x-show="open" 
                                            x-cloak 
                                            @click.outside="open = false" 
                                            x-transition 
                                            class="absolute right-0 mt-2 w-36 bg-white rounded-md shadow-lg z-10"
                                        >
                                            <ul class="text-gray-700 text-[12px] m-2">
                                                @if ($folder->folder_type_id == 1)
                                                    {{-- Pretest --}}
                                                    <li>
                                                        <a href="{{ route('admin.quiz.pretest.show', $folder->id) }}" 
                                                        class="block px-4 py-2 hover:bg-gray-100 rounded-md cursor-pointer">
                                                        Open Quiz
                                                        </a>
                                                    </li>
                                                @elseif ($folder->folder_type_id == 2)
                                                    {{-- Module --}}
                                                    <li>
                                                        <a href="{{ route('admin.module.show', $folder->id) }}?level_id=1" 
                                                        class="block px-4 py-2 hover:bg-gray-100 rounded-md cursor-pointer">
                                                        Easy
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.module.show', $folder->id) }}?level_id=2"
                                                        class="block px-4 py-2 hover:bg-gray-100 rounded-md cursor-pointer">
                                                        Average
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.module.show', $folder->id) }}?level_id=3"
                                                        class="block px-4 py-2 hover:bg-gray-100 rounded-md cursor-pointer">
                                                        Hard
                                                        </a>
                                                    </li>
                                                @elseif ($folder->folder_type_id == 3)
                                                    {{-- Post-test --}}
                                                    <li>
                                                        <a href="{{ route('admin.quiz.posttest.show', $folder->id) }}" 
                                                        class="block px-4 py-2 hover:bg-gray-100 rounded-md cursor-pointer">
                                                        Open Quiz
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="font-semibold text-gray-800 mb-2">{{ $label }}</p>
                                    <p class="text-[12px] text-gray-500">
                                        <span class="font-semibold">Updated: </span>{{ date('d M Y', strtotime($folder->updated_at)) }}
                                        &nbsp;&nbsp;
                                        <span class="font-semibold">Created: </span>{{ date('d M Y', strtotime($folder->created_at)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm col-span-3 text-center">
                            No folders yet.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-6">
                No projects yet.
            </div>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $all_projects->links() }}
    </div>
</div>
@endsection
