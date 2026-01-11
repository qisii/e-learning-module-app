@extends('layouts.app')

@section('title', 'Module')

@section('header', 'Module')

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 relative">

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
        <div class="flex items-center gap-2 bg-gray-100 rounded-xl relative">
            <button
                type="button"
                id="demo-guide-btn"
                title="Demo Guide"
                class="px-6 py-3 text-[14px] rounded-xl
                    bg-[#FB923C] text-white
                    shadow-md hover:bg-[#F97316]
                    transition duration-200
                    focus:outline-none focus:ring-2 focus:ring-[#FB923C]">

                <i class="ri-question-line text-lg"></i>
            </button>

            <a href="{{ route('admin.module.show', $folder->id) . '?level_id=' . $level_id }}"
                class="px-6 py-4 text-[12px] rounded-xl bg-[#E5E7EB] text-[#374151] font-semibold cursor-default"
                id="intro-form-tab"
                data-step="2"
                data-intro="This is the Form tab. You will create and edit the module content here."
            >
                Form
            </a>

            <a href="{{ route('admin.module.preview', $folder->id) . '?level_id=' . $level_id }}"
                class="px-6 py-4 text-[12px] rounded-xl text-[#6B7280] hover:text-[#374151] transition"
                id="intro-preview-tab"
                data-step="3"
                data-intro="Use Preview to check how the module will look to students."
                data-position="bottom"
            >
                Preview
            </a>
        </div>

    </div>

    {{-- Module Form Section --}}
    <livewire:module-handout :folder="$folder" :level_id="$level_id" />

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const demoBtn = document.getElementById('demo-guide-btn');

    if (demoBtn) {
        demoBtn.addEventListener('click', function () {
            introJs().setOptions({
                nextLabel: 'Next →',
                prevLabel: '← Back',
                doneLabel: 'Finish',
                skipLabel: 'Skip',
                showProgress: false,  // hide progress bar
                showBullets: true,    // show dots
                overlayOpacity: 0.65,
                exitOnOverlayClick: false,
                tooltipPosition: 'auto',
            })
            .onbeforechange(function(targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            })
            .start();
        });
    }
});
</script>
@endpush


@endsection
