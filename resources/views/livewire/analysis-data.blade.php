<div>
    <div class="flex items-center justify-between mb-4 text-gray-600">
        <div class="bg-orange-50 text-orange-700 px-4 py-2 rounded-lg shadow-sm text-md lg:text-lg font-medium flex items-center gap-2">
            🧐
            <span class="text-[11px] md:text-[13px] lg:text-sm">Visualizes student, project, and module data using charts</span>
        </div>

        <div class="flex items-center gap-2 text-[11px] md:text-[13px] lg:text-sm">
            <span class="text-md lg:text-lg">{{ $this->dateTime['icon'] }}</span>
            <span>{{ $this->dateTime['text'] }}</span>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">
            Student Demographics
        </h2>

        <button id="exportExcelBtn"
                class="text-white hover:text-white flex items-center cursor-pointer gap-1 text-[12px] 
                    bg-green-500 hover:bg-green-600 px-3 py-2 rounded shadow-sm">
            <i class="ri-export-line"></i> Export Excel
        </button>
    </div>

    {{-- Student Demographics --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6 space-y-6">
            {{-- Title --}}
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Students Overview</h3>
                <button id="downloadTotalChart"
                        class="text-gray-600 hover:text-gray-800 flex items-center cursor-pointer gap-1 text-[12px] 
                            bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded shadow-sm">
                    <i class="ri-arrow-down-line"></i> PNG
                </button>
            </div>

            {{-- Bar Chart --}}
            <div class="w-full h-70" wire:ignore>
                <canvas id="totalStudentsChart" class="w-full h-full"></canvas>
            </div>

            {{-- Numbers LARGE --}}
            <div class="hidden lg:flex flex-wrap gap-4 justify-start mb-0">
                {{-- Total Students --}}
                <div class="p-3 rounded-lg bg-blue-50 text-center flex-[2_1_100px]">
                    <p class="text-[12px] text-gray-600">Total Students</p>
                    <p class="text-xl font-bold text-blue-600" id="stat-total">
                        {{ $this->studentStats['total'] }}
                    </p>
                </div>

                {{-- Male --}}
                <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                    <p class="text-[12px] text-gray-500">Male</p>
                    <p class="font-semibold text-gray-800" id="stat-male">
                        {{ $this->studentStats['male'] }}
                    </p>
                </div>

                {{-- Female --}}
                <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                    <p class="text-[12px] text-gray-500">Female</p>
                    <p class="font-semibold text-gray-800" id="stat-female">
                        {{ $this->studentStats['female'] }}
                    </p>
                </div>

                {{-- Other --}}
                <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                    <p class="text-[12px] text-gray-500">Other</p>
                    <p class="font-semibold text-gray-800" id="stat-other">
                        {{ $this->studentStats['other'] }}
                    </p>
                </div>

                {{-- None --}}
                <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                    <p class="text-[12px] text-gray-500">None</p>
                    <p class="font-semibold text-gray-800" id="stat-none">
                        {{ $this->studentStats['none'] ?? 0 }}
                    </p>
                </div>
            </div>

            {{-- Numbers SMALLER SCREENS --}}
            <div class="flex flex-col gap-4 lg:hidden">
                {{-- Total Students (full width row) --}}
                <div class="p-3 rounded-lg bg-blue-50 text-center w-full">
                    <p class="text-[12px] text-gray-600">Total Students</p>
                    <p class="text-xl font-bold text-blue-600" id="stat-total">
                        {{ $this->studentStats['total'] }}
                    </p>
                </div>

                {{-- Other stats: Male, Female, Other, None --}}
                <div class="flex flex-wrap gap-4 justify-start">
                    {{-- Male --}}
                    <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                        <p class="text-[12px] text-gray-500">Male</p>
                        <p class="font-semibold text-gray-800" id="stat-male">
                            {{ $this->studentStats['male'] }}
                        </p>
                    </div>

                    {{-- Female --}}
                    <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                        <p class="text-[12px] text-gray-500">Female</p>
                        <p class="font-semibold text-gray-800" id="stat-female">
                            {{ $this->studentStats['female'] }}
                        </p>
                    </div>

                    {{-- Other --}}
                    <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                        <p class="text-[12px] text-gray-500">Other</p>
                        <p class="font-semibold text-gray-800" id="stat-other">
                            {{ $this->studentStats['other'] }}
                        </p>
                    </div>

                    {{-- None --}}
                    <div class="p-3 rounded-lg bg-gray-50 text-center flex-1 min-w-[80px]">
                        <p class="text-[12px] text-gray-500">None</p>
                        <p class="font-semibold text-gray-800" id="stat-none">
                            {{ $this->studentStats['none'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 relative">
            {{-- Download Button --}}
            <button id="downloadGenderChart" class="absolute top-6 right-4 text-gray-600 hover:text-gray-800 cursor-pointer flex items-center gap-1 text-[12px] z-10 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded shadow-sm">
                <i class="ri-arrow-down-line"></i> PNG
            </button>

            <div class="w-full h-90 mt-10 md:mt-[25%] lg:mt-8" wire:ignore>
                <canvas id="genderChart" class="w-full h-full"></canvas>
            </div>
        </div>
        {{-- End of  Student Demographics --}}
    </div>

    {{-- Pretest, Post-test, Modules Data --}}
    <div class="mt-10 space-y-6">

        {{-- Section Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Learning Performance Analysis <span class="text-[13px] text-gray-500 font-normal">(All Projects)</span>
            </h2>
        </div>

        {{-- Top Cards (responsive stacked on small screens) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Average Pretest Score --}}
            <div class="bg-white rounded-lg shadow p-5 text-center h-30 lg:h-32 flex flex-col justify-center">
                <p class="text-[12px] text-gray-500">Average Pretest Score</p>
                <p class="text-2xl font-bold text-blue-600 mt-2">
                    {{ $this->learningStats['avg_pretest'] }}%
                </p>
            </div>

            {{-- Average Post-test Score --}}
            <div class="bg-white rounded-lg shadow p-5 text-center h-30 lg:h-32 flex flex-col justify-center">
                <p class="text-[12px] text-gray-500">Average Post-test Score</p>
                <p class="text-2xl font-bold text-green-600 mt-2">
                    {{ $this->learningStats['avg_posttest'] }}%
                </p>
            </div>

            {{-- Module Completion Rate --}}
            <div class="bg-white rounded-lg shadow p-5 text-center h-30 lg:h-32 flex flex-col justify-center">
                <p class="text-[12px] text-gray-500">Module Completion Rate</p>
                <p class="text-[10px] text-gray-400 mb-2">
                    (Students who completed all levels at least once)
                </p>
                <p class="text-2xl font-bold text-orange-400 mt-1">
                    {{ $this->learningStats['module_completion'] }}%
                </p>
            </div>
        </div>

        {{-- Top Scorers --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Pretest Scorers --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    Top Pretest Scorers
                </h3>

                <table class="w-full table-fixed text-left text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($this->topScorers['pretest'] as $attempt)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="w-2/3 py-3 px-2">
                                    <div class="flex flex-row items-center gap-2">
                                        {{-- Medal placeholder: reserve space for all rows --}}
                                        <span class="w-5 text-center text-lg">
                                            @if($loop->index == 0) 🥇
                                            @elseif($loop->index == 1) 🥈
                                            @elseif($loop->index == 2) 🥉
                                            @endif
                                        </span>

                                        <div class="flex flex-col md:flex-row md:items-center gap-1">
                                            <span class="font-medium text-[13px] 
                                                @if($loop->index == 0) text-yellow-500
                                                @elseif($loop->index == 1) text-blue-400
                                                @elseif($loop->index == 2) text-orange-400
                                                @else text-gray-800 @endif
                                            ">
                                                {{ $attempt->user->first_name . ' ' . $attempt->user->last_name }}
                                            </span>
                                            <span class="text-gray-500 text-xs md:ml-2">
                                                {{ '@' . $attempt->user->username }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="w-1/3 py-3 px-2 text-gray-600 text-[13px]">
                                    {{ $attempt->quiz->folder->project->title ?? 'N/A' }}
                                </td>
                                <td class="w-1/3 py-3 px-2 font-semibold text-center 
                                    @if($loop->index == 0) text-yellow-500
                                    @elseif($loop->index == 1) text-blue-400
                                    @elseif($loop->index == 2) text-orange-400
                                    @else text-gray-800 @endif
                                ">
                                    {{ $attempt->score }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Top Post-test Scorers --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    Top Post Scorers
                </h3>

                <table class="w-full table-fixed text-left text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($this->topScorers['posttest'] as $attempt)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="w-2/3 py-3 px-2">
                                    <div class="flex flex-row items-center gap-2">
                                        {{-- Medal placeholder --}}
                                        <span class="w-5 text-center text-lg">
                                            @if($loop->index == 0) 🥇
                                            @elseif($loop->index == 1) 🥈
                                            @elseif($loop->index == 2) 🥉
                                            @endif
                                        </span>

                                        <div class="flex flex-col md:flex-row md:items-center gap-1">
                                            <span class="font-medium text-[13px] 
                                                @if($loop->index == 0) text-yellow-500
                                                @elseif($loop->index == 1) text-blue-400
                                                @elseif($loop->index == 2) text-orange-400
                                                @else text-gray-800 @endif
                                            ">
                                                {{ $attempt->user->first_name . ' ' . $attempt->user->last_name }}
                                            </span>
                                            <span class="text-gray-500 text-xs md:ml-2">
                                                {{ '@' . $attempt->user->username }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="w-1/3 py-3 px-2 text-gray-600 text-[13px]">
                                    {{ $attempt->quiz->folder->project->title ?? 'N/A' }}
                                </td>
                                <td class="w-1/3 py-3 px-2 font-semibold text-center 
                                    @if($loop->index == 0) text-yellow-500
                                    @elseif($loop->index == 1) text-blue-400
                                    @elseif($loop->index == 2) text-orange-400
                                    @else text-gray-800 @endif
                                ">
                                    {{ $attempt->score }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pretest, Post-test, Modules Data --}}
    <div class="mt-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Learning Performance Analysis <span class="text-[13px] text-gray-500 font-normal">(By Project)</span>
            </h2>

            <button id=""
                    class="text-white hover:text-white flex items-center cursor-pointer gap-1 text-[12px] 
                        bg-green-500 hover:bg-green-600 px-3 py-2 rounded shadow-sm">
                <i class="ri-export-line"></i> Export Excel
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            {{-- Pretest and Post-test --}}
            <div class="md:col-span-2 bg-white rounded-lg shadow p-6 space-y-6">
                {{-- Title --}}
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">Overall Pretest & Post-test</h3>
                    <button id=""
                            class="text-gray-600 hover:text-gray-800 flex items-center cursor-pointer gap-1 text-[12px] 
                                bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded shadow-sm">
                        <i class="ri-arrow-down-line"></i> PNG
                    </button>
                </div>

                {{-- Filters --}}
                <div class="flex flex gap-3 items-end">
                    {{-- Project Dropdown --}}
                    <div class="relative w-full">
                        <label class="text-xs text-gray-600">Project</label>
                        <select wire:model="projectId"
                                class="w-full mt-1 text-xs py-2 pl-3 pr-10 rounded-md shadow-sm appearance-none
                                    focus:ring-green-500 focus:border-green-500 bg-white
                                    border @error('projectId') border-red-500 @else border-gray-300 @enderror">
                            <option value="">Select a Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->title ?? 'Untitled Project' }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Custom Dropdown Icon -->
                        <div class="pointer-events-none absolute inset-y-0 right-3 top-8 flex items-center text-gray-500">
                            <i class="ri-arrow-down-s-line text-base"></i>
                        </div>
                    </div>

                    {{-- Start Attempt --}}
                    <div class="w-2/3">
                        <label class="text-xs text-gray-600">Start Attempt</label>
                        <input type="number"
                            wire:model="startAttempt"
                            min="1"
                            class="w-full mt-1 text-xs py-2 px-3 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500
                                border @error('startAttempt') border-red-500 @else border-gray-300 @enderror"
                            placeholder="Starts here (covers 10 tries)">
                    </div>

                    {{-- Show Button --}}
                    <div class="w-auto">
                        <button wire:click="filterPrePost"
                                class="w-full md:w-auto mt-5 md:mt-0
                                        text-xs text-white bg-blue-600 text-white rounded-md hover:bg-blue-700 transition
                                        px-4 py-2 shadow-sm flex items-center gap-1">
                            Show
                        </button>
                    </div>

                </div>

                <div class="flex gap-4 h-70" wire:ignore>
                    <div class="w-1/2 h-full">
                        <canvas id="pretestChart" class="w-full h-full"></canvas>
                    </div>
                    <div class="w-1/2 h-full">
                        <canvas id="posttestChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
            {{-- Modules --}}
            <div class="bg-white rounded-lg shadow p-6 relative">
                {{-- Download Button --}}
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">Overall Module Attempts per Level</h3>
                    <button id="downloadModuleChart"
                            class="text-gray-600 hover:text-gray-800 flex items-center cursor-pointer gap-1 text-[12px] 
                                bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded shadow-sm">
                        <i class="ri-arrow-down-line"></i> PNG
                    </button>
                </div>

                {{-- Project Filter --}}
                <div class="mt-2 flex items-end gap-3">
                    <div class="relative w-full">
                        <label class="text-xs text-gray-600">Project</label>
                        <select wire:model="projectIdModule"
                                class="w-full mt-1 text-xs py-2 pl-3 pr-10 rounded-md shadow-sm appearance-none
                                    focus:ring-green-500 focus:border-green-500 bg-white
                                    border @error('projectIdModule') border-red-500 @else border-gray-300 @enderror">
                            <option value="">Select a Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->title ?? 'Untitled Project' }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Custom Dropdown Icon -->
                        <div class="pointer-events-none absolute inset-y-0 right-3 top-8 flex items-center text-gray-500">
                            <i class="ri-arrow-down-s-line text-base"></i>
                        </div>
                    </div>
                    {{-- Show Button --}}
                    <div class="w-auto">
                        <button wire:click="filterModule"
                                class="w-full md:w-auto mt-5 md:mt-0
                                        text-xs text-white bg-blue-600 text-white rounded-md hover:bg-blue-700 transition
                                        px-4 py-2 shadow-sm flex items-center gap-1">
                            Show
                        </button>
                    </div>
                </div>

                {{-- Pie Chart --}}
                <div class="w-full h-90 mt-10 md:mt-[25%] lg:mt-8" wire:ignore>
                    <canvas id="moduleChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div wire:poll.3s style="display:none"></div>
</div>
