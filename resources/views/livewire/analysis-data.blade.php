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
        <div class="w-full h-60" wire:ignore>
            <canvas id="totalStudentsChart" class="w-full h-full"></canvas>
        </div>

        {{-- Numbers LARGE --}}
        <div class="hidden lg:flex flex-wrap gap-4 justify-start">
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

        <div class="w-full h-90 mt-3" wire:ignore>
            <canvas id="genderChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <div wire:poll.3s style="display:none"></div>
</div>
