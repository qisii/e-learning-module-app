@extends('layouts.app')

@section('title', 'Module')

@section('header', 'Module')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10">

    {{-- Go Back + Tabs Container --}}
    <div class="flex items-center justify-between mb-6">

        {{-- Back Button --}}
        <button type="button" 
                onclick="openBackModal()" 
                class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
                style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
        </button>

        @include('components.new-components.back-confirmation-modal')

        {{-- Tabs Section --}}
        <div class="flex items-center gap-2 bg-gray-100 rounded-xl">
            <a href="{{ route('admin.module.show', $folder->id) . '?level_id=' . $level_id }}"
                class="px-6 py-4 text-[12px] rounded-xl bg-[#E5E7EB] text-[#374151] font-semibold cursor-default">
                Form
            </a>

            <a href="{{ route('admin.module.preview', $folder->id) . '?level_id=' . $level_id }}"
                class="px-6 py-4 text-[12px] rounded-xl text-[#6B7280] hover:text-[#374151] transition">
                Preview
            </a>
        </div>

    </div>

    {{-- Module Form Section --}}
    <livewire:module-handout :folder="$folder" :level_id="$level_id" />

</div>
@endsection
