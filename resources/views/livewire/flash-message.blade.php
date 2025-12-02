<div>
    @php
        $colors = [
            'success' => [
                'bg' => 'from-green-100 to-green-50 border-green-300 text-green-800',
                'icon' => 'ri-checkbox-circle-fill text-green-600',
            ],
            'error' => [
                'bg' => 'from-red-100 to-red-50 border-red-300 text-red-800',
                'icon' => 'ri-error-warning-fill text-red-600',
            ],
            'warning' => [
                'bg' => 'from-yellow-100 to-yellow-50 border-yellow-300 text-yellow-800',
                'icon' => 'ri-alert-fill text-yellow-600',
            ],
            'info' => [
                'bg' => 'from-blue-100 to-blue-50 border-blue-300 text-blue-800',
                'icon' => 'ri-information-fill text-gray-600',
            ],
            'welcome' => [
                'bg' => 'from-blue-100 to-blue-50 border-blue-300 text-blue-800',
                'icon' => 'ri-emotion-happy-fill text-blue-600',
            ],
        ];

        $color = $colors[$type] ?? $colors['info'];
    @endphp

    @if ($message)
        <div 
            x-data="{ show: @entangle('visible') }"
            x-init="
                if (show) {
                    setTimeout(() => show = false, 3000);
                }
                $watch('show', value => {
                    if (value) setTimeout(() => show = false, 3000);
                });
            "
            x-show="show"
            x-transition:enter="transform transition ease-out duration-500"
            x-transition:enter-start="-translate-y-full opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-500"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="-translate-y-full opacity-0"
            class="fixed top-4 left-1/2 -translate-x-1/2 w-[90%] md:w-1/2 lg:w-1/2 p-4 max-w-md z-50"
        >
            <div class="flex items-center justify-between bg-gradient-to-br {{ $color['bg'] }} border px-4 py-3 rounded-xl shadow-md" style="font-family: 'Inter', sans-serif;">
                <div class="flex items-center gap-3">
                    <i class="{{ $color['icon'] }} text-lg"></i>
                    <p class="text-sm font-medium">
                        {{ $message }}
                    </p>
                </div>

                <!-- Close Icon -->
                <button 
                    @click="show = false" 
                    class="text-gray-500 hover:text-gray-700 transition"
                >
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('success') || session('error') || session('warning') || session('info'))
        @php
            $sessionType = session('success') ? 'success' :
                           (session('error') ? 'error' :
                           (session('warning') ? 'warning' : 'info'));

            $sessionMessage = session($sessionType);
            $sessionColor = $colors[$sessionType];
        @endphp

        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition:enter="transform transition ease-out duration-500"
            x-transition:enter-start="-translate-y-full opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-500"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="-translate-y-full opacity-0"
            class="fixed top-4 left-1/2 -translate-x-1/2 w-[90%] md:w-1/2 lg:w-1/2 p-4 max-w-md z-50"
        >
            <div class="flex items-center justify-between bg-gradient-to-br {{ $sessionColor['bg'] }} border px-4 py-3 rounded-xl shadow-md" style="font-family: 'Inter', sans-serif;">
                <div class="flex items-center gap-3">
                    <i class="{{ $sessionColor['icon'] }} text-lg"></i>
                    <p class="text-sm font-medium">
                        @if (is_array($sessionMessage) && isset($sessionMessage['name']))
                            <span class="font-bold">{{ $sessionMessage['name'] }}</span>
                            {{ str_replace($sessionMessage['name'], '', $sessionMessage['message']) }}
                        @else
                            {{ $sessionMessage }}
                        @endif
                    </p>
                </div>

                @php
                    $closeColor = match($sessionType) {
                        'success' => 'text-green-600 hover:text-green-800',
                        'error' => 'text-red-600 hover:text-red-800',
                        'warning' => 'text-yellow-600 hover:text-yellow-800',
                        'info' => 'text-blue-600 hover:text-blue-800',
                        default => 'text-gray-500 hover:text-gray-700',
                    };
                @endphp
                <button @click="show = false" class="{{ $closeColor }} transition">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
        </div>
    @endif
</div>
