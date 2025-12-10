<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

// class ProfileForm extends Component
// {

//     use WithFileUploads;

//     public User $user;

//     public $first_name;
//     public $last_name;
//     public $username;
//     public $phone_number;
//     public $gender;
//     public $city;
//     public $state_country;
//     public $email;
//     public $password;
//     public $avatar;
//     // public $type;
//     // public $message;
//     const LOCAL_STORAGE_FOLDER = 'avatars/';

//     public function mount(User $user)
//     {
//         $this->user = $user;

//         $this->first_name    = $user->first_name;
//         $this->last_name     = $user->last_name;
//         $this->username      = $user->username;
//         $this->phone_number  = $user->phone_number;
//         $this->gender        = $user->gender;
//         $this->city          = $user->city;
//         $this->state_country = $user->state_country;
//         $this->email         = $user->email;
//         $this->avatar        = $user->avatar;
//     }

//     // dd('updating');
//     // dd([
//     //     'user_id' => $this->user->id,
//     //     'first_name' => $this->first_name,
//     //     'last_name' => $this->last_name,
//     //     'username' => $this->username,
//     //     'phone_number' => $this->phone_number,
//     //     'gender' => $this->gender,
//     //     'city' => $this->city,
//     //     'state_country' => $this->state_country,
//     //     'email' => $this->email,
//     //     'password' => $this->password,
//     //     'avatar' => $this->avatar,
//     //     'role_id' => $this->user->role_id,
//     // ]);


//     /**
//      * 
//      * ISSUE
//      * 
//      * will only update if there is a new uploaded image.
//      */
//     public function updateUser()
//     {
//         // Validation rules (same as before)
//         $this->validate([
//             'first_name' => 'required|max:50',
//             'last_name' => 'required|max:50',
//             'phone_number' => 'nullable',
//             'gender' => 'nullable',
//             'city' => 'nullable',
//             // 'email' => 'nullable',
//             'username' => 'required|max:20|unique:users,username,' . $this->user->id,
//             'state_country' => 'nullable',
//             'password'      => 'nullable|min:8',
//             'avatar' => 'nullable|mimes:jpeg,jpg,png,gif|max:1048',
//         ]);

//         $user = $this->user->findOrFail($this->user->id);
//         // dd($user);

//         // Update user fields
//         $user->first_name    = $this->first_name;
//         $user->last_name     = $this->last_name;
//         $user->username      = $this->username;
//         $user->phone_number  = $this->phone_number;
//         $user->gender        = $this->gender;
//         $user->city          = $this->city;
//         $user->state_country = $this->state_country;
//         // $user->email         = $this->email;

//         if (!empty($this->password)) {
//             $user->password = Hash::make($this->password);
//         }

//         // Avatar upload
//         if ($this->avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) { 
//             if ($this->user->avatar) { 
                
//                 $this->deleteAvatar($this->user->avatar); 
//             } 
//             $user->avatar = $this->saveAvatar($this->avatar); 
//         }

//         $user->save();
//     }

//     private function saveAvatar($avatar){
//         $avatar_name = time() . "." . $avatar->extension();
//         $avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name);
//         return $avatar_name;
//     }

//     private function deleteAvatar($avatar){
//         $avatar_path = self::LOCAL_STORAGE_FOLDER . $avatar;

//         if(Storage::disk('public')->exists($avatar_path)){
//             Storage::disk('public')->delete($avatar_path);
//         }
//     }


//     public function render()
//     {
//         // dd($this->first_name);
//         return view('livewire.profile-form');
//     }
// }


class ProfileForm extends Component
{
    use WithFileUploads;

    public User $user;

    public $first_name;
    public $last_name;
    public $username;
    public $phone_number;
    public $gender;
    public $city;
    public $state_country;
    public $email;
    public $password;
    public $grade_level;
    public $section;

    public $avatar; // new upload
    // public $currentAvatar; // filename from DB

    const LOCAL_STORAGE_FOLDER = 'avatars/';

    public function mount(User $user)
    {
        $this->user = $user;

        $this->first_name    = $user->first_name;
        $this->last_name     = $user->last_name;
        $this->username      = $user->username;
        $this->phone_number  = $user->phone_number;
        $this->gender        = $user->gender;
        $this->grade_level   = $user->grade_level;
        $this->section       = $user->section;
        $this->city          = $user->city;
        $this->state_country = $user->state_country;
        $this->email         = $user->email;
        // $this->currentAvatar = $user->avatar; // save old filename
        $this->avatar        = null;
    }

    public function updateUser()
    {
        // dd($this->avatar);
        $this->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'username' => 'required|max:20|unique:users,username,' . $this->user->id,
            'password' => 'nullable|min:8',
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif|max:1048',
        ], [
            'avatar.max' => 'Max image size is 1048kb',
        ]);

        $user = $this->user->findOrFail($this->user->id);

        $user->first_name    = $this->first_name;
        $user->last_name     = $this->last_name;
        $user->username      = $this->username;
        $user->phone_number  = $this->phone_number;
        $user->gender        = $this->gender;
        $user->grade_level   = $this->grade_level;
        $user->section       = $this->section;
        $user->city          = $this->city;
        $user->state_country = $this->state_country;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        if ($this->avatar) {

            $extension = $this->avatar->extension();

            $base64 = 'data:image/' . $extension . ';base64,' .
                    base64_encode($this->avatar->get());

            $user->avatar = $base64;
        }

        $user->save();

        // session()->flash('message', 'Profile updated successfully!');
        $this->dispatch('flashMessage', type: 'success', message: 'Profile updated successfully!');

    }

    private function saveAvatar($avatar)
    {
        $avatar_name = time() . "." . $avatar->extension();
        $avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name, 'public');
        return $avatar_name;
    }

    private function deleteAvatar($avatar)
    {
        $path = self::LOCAL_STORAGE_FOLDER . $avatar;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function render()
    {
        return view('livewire.profile-form');
    }
}

