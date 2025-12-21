@extends('layouts.app')

@section('title','Module Handout')
@section('header', 'Module Handout')
@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar">
    <div class="flex items-center justify-between mb-6">
        {{-- Go Back Button --}}
        <button type="button" 
                onclick="openBackModal()" 
                class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
                style="font-family: 'Inter', sans-serif;">
            <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
            <span class="font-medium">Go Back</span>
        </button>

        {{-- Download Button --}}
        @if(!empty($gdriveLink))
            <a href="{{ $gdriveLink }}" 
            target="_blank" 
            title="Download from Google Drive"
            class="px-6 py-4 text-[12px] rounded-xl bg-purple-100 text-purple-800 border border-purple-200 shadow-sm 
                    hover:bg-purple-200 hover:shadow-md transition-all duration-200 flex items-center gap-2 font-secondary">
            
                <i class="ri-external-link-line text-[14px]"></i> 
                Download from Google Drive
            </a>
        @else
            <span class="px-6 py-4 text-[12px] rounded-xl bg-gray-100 text-gray-400 border border-gray-200 
                        font-semibold flex items-center gap-2 transition-all duration-200 font-secondary">
                <i class="ri-error-warning-line text-[14px]"></i>
                No Google Drive link available
            </span>
        @endif

    </div>

    @include('components.new-components.back-confirmation-user-modal')
    

    @if($handout)
        @php
            $currentPageComponentsCount = $pages->first() ? $pages->first()->components->count() : 0;
            $isFirstPage = $pages->currentPage() === 1;
            $hasContent = $currentPageComponentsCount > 0;

            // collect hidden objectives
            $hiddenObjectives = [];
            $objectiveTargets = [];

            $hasHiddenObjectives = false;
        @endphp

        @if($isFirstPage && !$hasContent)
            {{-- First page empty → show "Almost ready!" --}}
            <div class="w-[90%] lg:w-[80%] mx-auto mt-[20%] lg:mt-[10%] flex flex-col items-center justify-center text-center">
                <div class="text-7xl mb-6 animate-bounce">📘</div>

                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                    Almost ready!
                </h1>

                <p class="text-gray-600 text-lg mb-8" style="font-family: 'Inter', sans-serif;">
                    The module is on its way. Please check back soon once the content has been added.
                </p>
            </div>
        @else
            {{-- TIMER DISPLAY --}}
            <div class="text-center mb-4 hidden">
                <h3 class="text-lg font-semibold text-gray-700">
                    Time Spent: <span id="handout-timer" class="text-blue-600">00:00:00</span>
                </h3>
            </div>

            {{-- MAIN WRAPPER --}}
            <div id="module-wrapper"
                class="relative w-[90%] mx-auto max-h-[1056px] overflow-auto no-scrollbar mt-[4%] px-0 lg:px-5 py-10 rounded-lg bg-white shadow-md bg-cover bg-center">

                {{-- Title Header --}}
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-gray-800 font-secondary">
                        Module Handout
                    </h2>
                </div>

                {{-- HANDOUT CONTENT --}}
                <div class="my-10 space-y-6 mx-8 overflow-auto">
                    @if($hasContent)
                        @foreach ($pages as $page)
                            @foreach ($page->components as $component)
                                @php
                                    $data = json_decode($component->data, true);
                                @endphp

                                {{-- TEXT COMPONENT --}}
                                @if ($component->type === 'text')
                                    <div class="prose max-w-none handout-text">
                                        {!! $data['content'] ?? '' !!}
                                    </div>
                                {{-- HIDDEN OBJECTIVE --}}
                                @elseif ($component->type === 'objective')
                                    @php
                                        $hiddenObjectives[] = $data;
                                        $hasHiddenObjectives = true;

                                        if (!empty($data['targets'])) {
                                            foreach ($data['targets'] as $target) {
                                                $objectiveTargets[] = [
                                                    'id' => $target['target_id'],
                                                    'content' => $target['content'],
                                                    'completion_message' => $data['completion_message'] ?? 'Objective completed!'
                                                ];

                                            }
                                        }
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                    @else
                        {{-- Empty page placeholder --}}
                        <div class="w-full h-64 flex items-center justify-center text-gray-500">
                            <p>No content on this page.</p>
                        </div>
                    @endif
                </div>

                {{-- HIDDEN OBJECTIVES DISPLAY --}}
                @if (!empty($hiddenObjectives))
                    <div class="mt-12 mx-8 border-t pt-6">
                        <div class="space-y-4">
                            @foreach ($hiddenObjectives as $objective)
                                <div
                                    class="p-4 bg-white border-2 border-orange-200 rounded-lg
                                        font-secondary shadow-sm hidden-objective-glow">
                                    <p class="text-sm text-gray-700">
                                        {{ $objective['instruction'] ?? 'Objective instruction not available.' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        @endif

        {{-- PAGINATION (hidden if there are hidden objectives) --}}
        @if($hasContent)
            <div id="pagination-wrapper" class="w-[90%] mx-auto mt-8 {{ $hasHiddenObjectives ? 'hidden' : '' }}">
                {{ $pages->links() }}
            </div>
        @endif

        {{-- BOTTOM BUTTONS — ONLY ON LAST PAGE --}}
        @if ($pages->onLastPage())
            <div id="bottom-buttons-wrapper" style="{{ $hasHiddenObjectives ? 'display:none;' : '' }}">
                <p class="text-gray-500 text-[13px] text-center mt-10" style="font-family: 'Inter', sans-serif;">
                    You may access other modules.
                </p>

                <div class="flex flex-col items-center mt-4 px-4">
                    {{-- Other Module buttons --}}
                    @if ($level_id == 1)
                        <form class="module-form" action="{{ route('projects.module.attempt.store', ['handout_id' => $handout->id]) }}" method="post">
                            @csrf
                            <input type="hidden" name="seconds" class="secondsInput" value="0">
                            <input type="hidden" name="next_page" value="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 2]) }}">
                            <a href="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 2]) }}"
                                class="submit-link inline-flex items-center justify-center text-sm py-3 mb-3
                                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                                    text-white rounded-lg transition-all duration-500 
                                    ease-in-out w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
                                style="font-family: 'Inter', sans-serif;">
                                Check Average Module
                                <i class="ri-arrow-right-line ml-3"></i>
                            </a>
                        </form>
                    @elseif ($level_id == 2)
                        <form class="module-form" action="{{ route('projects.module.attempt.store', ['handout_id' => $handout->id]) }}" method="post">
                            @csrf
                            <input type="hidden" name="seconds" class="secondsInput" value="0">
                            <input type="hidden" name="next_page" value="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 3]) }}">
                            <a href="{{ route('projects.module.show', ['project_id' => $project_id, 'level_id' => 3]) }}"
                                class="submit-link inline-flex items-center justify-center text-sm py-3 mb-3
                                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                                    text-white rounded-lg transition-all duration-500 
                                    ease-in-out w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
                                style="font-family: 'Inter', sans-serif;">
                                Check Hard Module
                                <i class="ri-arrow-right-line ml-3"></i>
                            </a>
                        </form>
                    @endif
                    {{-- Post Test Button --}}
                    <form class="module-form" action="{{ route('projects.module.attempt.store', ['handout_id' => $handout->id]) }}" method="post">
                        @csrf
                        <input type="hidden" name="seconds" class="secondsInput" value="0">
                        <input type="hidden" name="next_page" value="{{ route('projects.welcome.posttest', $project_id) }}">
                        <a href="{{ route('projects.welcome.posttest', $project_id) }}"
                            class="submit-link inline-flex items-center justify-center text-sm py-3
                                bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                                text-white rounded-lg transition-all duration-500 
                                ease-in-out w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
                            style="font-family: 'Inter', sans-serif;">
                            Continue to Post Test
                            <i class="ri-arrow-right-line ml-3"></i>
                        </a>
                    </form>
                </div>
            </div>
        @endif

    @else
        {{-- HANDOUT NOT FOUND --}}
        <div class="w-[90%] lg:w-[80%] mx-auto mt-[20%] lg:mt-[10%] flex flex-col items-center justify-center text-center">
            <div class="text-7xl mb-6 animate-bounce">📘</div>

            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mb-3">
                Module not found
            </h1>

            <p class="text-gray-600 text-lg mb-8">
                The module is on its way. Please check back soon once the content has been added.
            </p>
        </div>
    @endif

    {{-- Hidden Objective Completion Dialog --}}
    <div id="objective-dialog"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div
            id="objective-dialog-box"
            class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl
                flex overflow-hidden m-3">

            {{-- LEFT: Character / Image --}}
            <div class="w-1/3 bg-gradient-to-b from-orange-100 to-orange-200
                        flex items-end justify-center p-4">
                <img
                    src="{{ asset('assets/images/illustration5.png') }}"
                    alt="Guide"
                    class="h-full object-contain drop-shadow-lg">
            </div>

            {{-- RIGHT: Dialogue --}}
            <div class="w-2/3 p-6 flex flex-col justify-between">

                {{-- Title --}}
                <div class="mb-2">
                    <h3 class="lg:text-lg text-[12px] lg:text-lg font-bold text-gray-800 font-secondary">
                        🎯 Objective Completed
                    </h3>
                </div>

                {{-- Message Bubble --}}
                <div
                    class="relative bg-gray-100 rounded-lg p-2 lg:p-4 text-[12px] lg:text-sm text-gray-700
                        shadow-inner before:content-['']
                        before:absolute before:-left-2 before:top-6
                        before:w-0 before:h-0
                        before:border-t-8 before:border-b-8
                        before:border-r-8
                        before:border-t-transparent
                        before:border-b-transparent
                        before:border-r-gray-100">

                    <p id="objective-dialog-message" class="leading-relaxed"></p>
                </div>

                {{-- Actions --}}
                <div class="mt-4 text-right">
                    <button
                        id="objective-dialog-ok"
                        type="button"
                        class="px-3 py-2 lg:px-5 text-[10px] cursor-pointer lg:text-md
                            bg-orange-500 text-white rounded-lg
                            hover:bg-orange-600 transition font-semibold font-secondary">
                        Continue
                    </button>
                </div>
            </div>

        </div>
    </div>

    <canvas
        id="confetti-canvas"
        class="fixed inset-0 pointer-events-none z-50 hidden">
    </canvas>

    {{-- TIMER SCRIPT --}}
    <script>
        const HANDOUT_ID = "{{ $project_id }}_{{ $level_id }}";
        const TIMER_KEY = "handout_timer_" + HANDOUT_ID;

        // Time running flag
        let timeRunning = true;

        // Load previous timer or start at 0
        let totalSeconds = parseInt(sessionStorage.getItem(TIMER_KEY)) || 0;

        function updateTimer() {
            console.log('timeRunning:', timeRunning); // <-- add this
            if (!timeRunning) {
                document.getElementById("handout-timer").textContent = "00:00:00";
                totalSeconds = 0;
                sessionStorage.removeItem(TIMER_KEY);
                return;
            }

            let hours = Math.floor(totalSeconds / 3600);
            let minutes = Math.floor((totalSeconds % 3600) / 60);
            let seconds = totalSeconds % 60;

            document.getElementById("handout-timer").textContent =
                String(hours).padStart(2, '0') + ":" +
                String(minutes).padStart(2, '0') + ":" +
                String(seconds).padStart(2, '0');

            totalSeconds++;
            sessionStorage.setItem(TIMER_KEY, totalSeconds);
        }

        setInterval(updateTimer, 1000);

        // Stop timer before form submission
        document.querySelectorAll('.submit-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                if (!form) return;

                const input = form.querySelector('.secondsInput');
                if (input) input.value = totalSeconds;

                // Stop timer
                timeRunning = false;

                // Submit the form
                form.submit();
            });
        });

        // --- Random background image for main wrapper ---
        const bgImages = [
            'module-bg.png',
            'module-bg-1.png',
            'module-bg-2.png',
            'module-bg-3.png'
        ];

        const wrapper = document.getElementById('module-wrapper');
        if (wrapper) {
            const randomIndex = Math.floor(Math.random() * bgImages.length);
            const bgUrl = "{{ asset('assets/images') }}/" + bgImages[randomIndex];
            wrapper.style.backgroundImage = `url('${bgUrl}')`;
        }

        // Hidden Objective
        window.hiddenObjectiveTargets = @json($objectiveTargets);
    </script>


</div>
@endsection
