<div>
    <form wire:submit="store" class="w-full px-6 py-2 lg:py-6" enctype="multipart/form-data">

        <div class="md:col-span-2 bg-[#FFF9E6] border border-yellow-300 text-yellow-800 rounded-lg p-5 mb-5">
            <div class="flex items-center mb-2">
                <i class="ri-information-fill text-yellow-600 text-lg mr-2"></i>
                <h2 class="text-[15px] font-semibold" style="font-family: 'Poppins', sans-serif;">
                    Reminder
                </h2>
            </div>
            <p class="text-[13px] leading-relaxed" style="font-family: 'Inter', sans-serif;">
                Every module will only have <strong>three folders</strong>:
                <span class="text-yellow-700 font-semibold">Pretest</span>, 
                <span class="text-yellow-700 font-semibold">Module</span>, and 
                <span class="text-yellow-700 font-semibold">Post-test</span>.
            </p>
        </div>

        <div class="relative mb-6">
            <label for="title" 
                class="block text-[14px] text-gray-600 mb-2 font-medium"
                style="font-family: 'Inter', sans-serif;">
                Title
            </label>

            <input 
                type="text" 
                id="title" 
                name="title"
                wire:model="title"
                autofocus
                class="w-full p-3 bg-white border border-gray-200 text-[14px] rounded-lg shadow-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                    transition-all duration-200 ease-in-out placeholder:text-gray-400
                    @error('title') border border-red-500 focus:ring-red-300 @enderror"
                placeholder="Enter your project title"
                style="font-family: 'Inter', sans-serif;"
            >

            @error('title')
                <span 
                    class="absolute left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                    style="font-family: 'Poppins', sans-serif;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="relative mb-6">
            <label for="description" 
                class="block text-[14px] text-gray-600 mb-2 font-medium"
                style="font-family: 'Inter', sans-serif;">
                Description
            </label>

            <textarea 
                id="description" 
                name="description"
                wire:model="description"
                rows="5"
                placeholder="Write a short description..."
                class="w-full p-3 bg-white border border-gray-200 text-[14px] rounded-lg resize-none shadow-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                    transition-all duration-200 ease-in-out placeholder:text-gray-400
                    @error('description') border border-red-500 focus:ring-red-300 @enderror"
                style="font-family: 'Inter', sans-serif;"
            ></textarea>

            @error('description')
                <span 
                    class="absolute left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                    style="font-family: 'Poppins', sans-serif;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="relative mb-8">
            <label class="block text-[14px] text-gray-600 mb-2 font-medium"
                    style="font-family: 'Inter', sans-serif;">
                Select Folders
            </label>

            <div class="flex flex-wrap gap-6 mt-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" wire:model="folders" value="1"
                            class="w-5 h-5 accent-blue-500 focus:ring-blue-400 border-gray-300 rounded-md">
                    <span class="text-[14px] text-gray-700" style="font-family: 'Inter', sans-serif;">Pretest</span>
                </label>

                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" wire:model="folders" value="2"
                            class="w-5 h-5 accent-blue-500 focus:ring-blue-400 border-gray-300 rounded-md">
                    <span class="text-[14px] text-gray-700" style="font-family: 'Inter', sans-serif;">Module</span>
                </label>

                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" wire:model="folders" value="3"
                            class="w-5 h-5 accent-blue-500 focus:ring-blue-400 border-gray-300 rounded-md">
                    <span class="text-[14px] text-gray-700" style="font-family: 'Inter', sans-serif;">Post-test</span>
                </label>
            </div>

            @error('folders')
                <span 
                    class="absolute left-0 top-full mt-1 text-red-500 text-[11px] whitespace-nowrap"
                    style="font-family: 'Poppins', sans-serif;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <button type="submit"
            class="w-full py-3 mb-4 lg:mb-0 bg-[#10B981] text-[#F9FAFB] text-[14px] font-medium rounded-md 
                hover:bg-[#065F46] transition duration-200 block cursor-pointer"
            style="font-family: 'Inter', sans-serif;">
            Save
        </button>
    </form>
</div>
