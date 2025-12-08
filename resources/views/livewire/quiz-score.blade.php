<div x-data="{ loading: @entangle('loading') }"
     x-init="if(loading) { setTimeout(() => { @this.finishLoading() }, 4000) }">

    <template x-if="loading">
        <div class="w-[90%] lg:w-[80%] mx-auto mt-[20%] flex flex-col items-center justify-center text-center space-y-6">
            
            {{-- Loading heading --}}
            <h1 class="text-3xl lg:text-3xl font-extrabold text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                 {{ $attempted ? 'Checking your previous pretest records...' : 'Processing your answers...' }}  
            </h1>

            {{-- Optional subtext --}}
            <p class="text-gray-600 text-lg" style="font-family: 'Inter', sans-serif;">
                {{ $attempted ? 'Please wait while we get your score...' : 'Please wait while we calculate your score.' }}
            </p>

            {{-- Spinner animation --}}
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-300 border-t-blue-500 mx-auto"></div>
        </div>
    </template>

    <template x-if="!loading">
        <div>
            <button type="button" 
                onclick="openBackModal()" 
                class="inline-flex items-center text-[14px] text-[#6B7280] hover:text-[#374151] transition"
                style="font-family: 'Inter', sans-serif;">
                    <i class="ri-arrow-left-line text-lg mr-2 border-2 border-[#E5E7EB] rounded-lg py-2 px-3"></i>
                    <span class="font-medium">Go Back</span>
            </button>

            @include('components.new-components.back-confirmation-user-modal')

            
            <div class="relative w-[90%] mx-auto mt-[6%] px-2 py-15 lg:p-20 rounded-lg shadow-md flex flex-col items-center justify-center bg-cover bg-top" style="background-image: url('{{ asset('assets/images/score-bg-3.png') }}');">

                <div class="w-[90%] lg:w-[80%] mx-auto text-center space-y-6 mt-[5%]">

                    <div class="text-6xl mb-4 animate-bounce">{{ $emoji }}</div>

                    <h2 class="text-xl lg:text-3xl font-extrabold {{ $color }}"
                        style="font-family: 'Poppins', sans-serif;">
                        {{ $message }}
                    </h2>

                    {{-- Score Details --}}
                    <div class="space-y-2 text-sm lg:text-lg" style="font-family: 'Poppins', sans-serif;">
                        <p class="text-gray-800">
                            Quiz: <strong>{{ $quizAttempt->quiz->folder->project->title }} PRETEST QUIZ</strong>
                        </p>
                        <p class="text-gray-800">
                            Score: <strong>{{ $quizAttempt->score }} / {{ $quizAttempt->quiz->questions->count() }}</strong>
                        </p>
                        <p class="text-gray-800">
                            Time Spent: <strong>{{ gmdate('i:s', $quizAttempt->time_spent) }}</strong>
                        </p>
                    </div>

                    <p class="text-gray-500 text-[10px] lg:text-[13px]" style="font-family: 'Inter', sans-serif;">
                        Based on your score, you may now access the recommended module.
                    </p>

                    {{-- Continue Button or Message --}}
                    @if (!empty($project_id) && !empty($recommendedLevel))
                        <a href="{{ route('projects.module.show', [
                                    'project_id' => $project_id,
                                    'level_id' => $recommendedLevel
                                ]) }}"
                            class="inline-flex items-center justify-center text-sm py-3
                                    bg-gradient-to-r from-blue-500 to-blue-900 bg-[length:150%_150%] bg-left 
                                    text-white rounded-lg transition-all duration-500 
                                    ease-in-out w-full lg:w-60 md:w-60 mx-auto hover:bg-right hover:shadow-lg transform hover:-translate-y-1"
                            style="font-family: 'Inter', sans-serif;">
                            Check Module
                            <i class="ri-arrow-right-line ml-3"></i>
                        </a>
                    @else
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-300 rounded-xl shadow-sm">
                            <p class="text-blue-800 text-base text-sm font-semibold text-center" 
                            style="font-family: 'Inter', sans-serif;">
                                🚧 The module is still on its way. Please check back soon!
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
