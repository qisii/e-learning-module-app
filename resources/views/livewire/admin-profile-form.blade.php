<div>
    <form wire:submit="updateUser" class="w-full px-6 pt-6 pb-3" enctype="multipart/form-data">

        {{-- Use 1 column on mobile, 3 columns on md/lg --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Left Column --}}
            <div class="space-y-4 order-2 md:order-1">

                {{-- First Name --}}
                <div class="relative mb-5">
                    <label for="first-name" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">First Name</label>
                    <input 
                        type="text" 
                        id="first-name" 
                        name="first_name"
                        wire:model="first_name"
                        placeholder="Enter first name"
                        class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                            transition-all duration-200 ease-in-out placeholder:text-gray-400 
                            @error('first_name') border border-red-500 focus:ring-red-300 @enderror"
                        style="font-family: 'Inter', sans-serif;"
                    >
                    @error('first_name')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div class="relative mb-5">
                    <label for="last-name" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">Last Name</label>
                    <input 
                        type="text" 
                        id="last-name" 
                        name="last_name"
                        wire:model="last_name"
                        placeholder="Enter last name"
                        class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                            transition-all duration-200 ease-in-out placeholder:text-gray-400 
                            @error('last_name') border border-red-500 focus:ring-red-300 @enderror"
                        style="font-family: 'Inter', sans-serif;"
                    >
                    @error('last_name')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone Number --}}
                <div class="relative mb-5">
                    <label for="phone-number" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">Phone Number</label>
                    <input 
                        type="text" 
                        id="phone-number" 
                        name="phone_number"
                        wire:model="phone_number"
                        placeholder="Enter phone number"
                        class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                            transition-all duration-200 ease-in-out placeholder:text-gray-400 
                            @error('phone_number') border border-red-500 focus:ring-red-300 @enderror"
                        style="font-family: 'Inter', sans-serif;"
                    >
                    @error('phone_number')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="relative mb-5">
                    <label for="gender" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">Gender</label>
                    <div class="relative">
                        <select id="gender" wire:model="gender"
                            class="appearance-none w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                                transition-all duration-200 ease-in-out text-gray-700
                                @error('gender') border border-red-500 focus:ring-red-300 @enderror"
                            style="font-family: 'Inter', sans-serif;">
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <i class="ri-arrow-down-s-line absolute right-3 top-[14px] text-gray-500 pointer-events-none"></i>
                    </div>
                    @error('gender')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-4 order-3 md:order-2">

                {{-- City --}}
                <div class="relative mb-5">
                    <label for="city" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">City</label>
                    <input 
                        type="text" 
                        id="city" 
                        name="city"
                        wire:model="city"
                        placeholder="Enter city"
                        class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                            transition-all duration-200 ease-in-out placeholder:text-gray-400 
                            @error('city') border border-red-500 focus:ring-red-300 @enderror"
                        style="font-family: 'Inter', sans-serif;"
                    >
                    @error('city')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>

                {{-- State / Country --}}
                <div class="relative mb-5">
                    <label for="state-country" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">State / Country</label>
                    <input 
                        type="text" 
                        id="state-country" 
                        name="state_country"
                        wire:model="state_country"
                        placeholder="Enter state or country"
                        class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                            transition-all duration-200 ease-in-out placeholder:text-gray-400 
                            @error('state_country') border border-red-500 focus:ring-red-300 @enderror"
                        style="font-family: 'Inter', sans-serif;"
                    >
                    @error('state_country')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="relative mb-5">
                    <label for="email" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        wire:model="email"
                        placeholder="Enter email address"
                        class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                            transition-all duration-200 ease-in-out placeholder:text-gray-400 
                            @error('email') border border-red-500 focus:ring-red-300 @enderror"
                        style="font-family: 'Inter', sans-serif;"
                    >
                    @error('email')
                        <span class="absolute left-0 top-full mt-1 text-red-500 text-[11px] font-secondary">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-10 md:mb-5 lg:mb-5 relative">
                    <label for="password" class="block text-[14px] text-gray-600 mb-2 font-medium font-secondary">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            wire:model="password"
                            placeholder="{{ empty($user->password) ? 'Please enter a password' : 'Enter new password (leave blank to keep current)' }}"
                            class="w-full p-3 bg-white border border-gray-200 text-[13px] rounded-lg shadow-sm 
                                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                                transition-all duration-200 ease-in-out placeholder:text-gray-400 text-gray-700
                                {{ session('password') ? 'border border-red-500 focus:ring-red-300' : '' }}"
                            style="font-family: 'Inter', sans-serif;"
                        >
                        @if (session('password'))
                            <span class="absolute left-0 top-full mt-1 text-red-500 text-[12px] font-secondary">{{ session('password') }}</span>
                        @endif
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 mb-5 bg-[#3B82F6] text-[#F9FAFB] text-[14px] font-medium rounded-md 
                        hover:bg-[#1E40AF] transition duration-200 block lg:hidden md:hidden cursor-pointer font-secondary">
                    Update
                </button>
            </div>


            {{-- Profile Image + Submit Button --}}
            <div class="flex flex-col items-center justify-between order-1 md:order-3">
                <div 
                    x-data="{ preview: null }" 
                    class="flex-grow flex flex-col items-center justify-center mb-6 md:mb-0"
                >
                    <div class="relative w-32 h-32">
                        <template x-if="preview">
                            <img :src="preview" alt="Preview"
                                 class="absolute inset-0 w-full h-full object-cover rounded-full border-4 border-blue-100">
                        </template>

                        <template x-if="!preview">
                            <div>
                                @if($currentAvatar)
                                    <img src="{{ Storage::url('avatars/' . $currentAvatar) }}" alt="{{ $first_name . ' ' . $last_name }}" 
                                         class="absolute inset-0 w-full h-full object-cover rounded-full border-4 border-blue-100">
                                @else
                                    <div class="absolute inset-0 rounded-full bg-blue-100 flex items-center justify-center overflow-hidden">
                                        <i class="ri-image-line text-blue-600 text-3xl"></i>
                                    </div>
                                @endif
                            </div>
                        </template>

                        <label for="avatar" class="absolute w-10 h-10 bottom-0 right-0 bg-blue-500 rounded-full pt-[.3em] text-center border-3 border-white cursor-pointer hover:bg-blue-600 transition">
                            <i class="ri-pencil-line text-white text-[16px]"></i>
                        </label>
                        <input type="file" id="avatar" wire:model="avatar" wire:key="avatar-{{ $user->id }}" class="hidden" accept="image/*"
                               @change="preview = URL.createObjectURL($event.target.files[0])">
                    </div>
                    {{-- <form action="{{ route('admin.profile.avatar.destroy') }}" method="post" class="mt-3">
                        @csrf
                        @method('DELETE')
                        
                        <button 
                            type="submit" 
                            class="px-4 py-1 text-[10px] mt-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition"
                            style="font-family: 'Inter', sans-serif;"
                        >
                            Remove Image
                        </button>
                    </form> --}}
                </div>

                <button type="submit"
                    class="w-full py-3 mb-5 bg-[#3B82F6] text-[#F9FAFB] text-[14px] font-medium rounded-md 
                        hover:bg-[#1E40AF] transition duration-200 hidden lg:block md:block cursor-pointer font-secondary">
                    Update
                </button>
            </div>
        </div>
    </form>
    {{-- <div class="" style="">
        <form action="{{ route('admin.profile.avatar.destroy') }}" method="post"
            class="">
            @csrf
            @method('DELETE')

            <button 
                type="submit"
                class="px-4 py-1 text-[10px] bg-red-500 text-white rounded-md hover:bg-red-600 transition shadow-md"
                style="font-family: 'Inter', sans-serif;">
                Remove Image
            </button>
        </form>
    </div> --}}
</div>
