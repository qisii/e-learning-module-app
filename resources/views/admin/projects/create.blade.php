@extends('layouts.app')

@section('title', 'Create Project')

@section('header', 'Create Project')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar">
    <div class="mb-6">
        <a href="{{ route('admin.projects') }}" 
        class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
        style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
        </a>
    </div>

    {{-- Create Form Section --}}
    <div class="bg-white w-[70%] lg:w-[50%] mx-auto shadow-lg rounded-lg p-0 pt-6 lg:p-6 md:p-6 mb-6 lg:mb-0">
        <livewire:project-form />
    </div>
</div>
@endsection
