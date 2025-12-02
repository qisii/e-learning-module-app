<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminProfileForm extends Component
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

    public $avatar; // new upload
    public $currentAvatar; // filename from DB

    const LOCAL_STORAGE_FOLDER = 'avatars/';

    public function mount(User $user)
    {
        $this->user = $user;

        $this->first_name    = $user->first_name;
        $this->last_name     = $user->last_name;
        $this->username      = $user->username;
        $this->phone_number  = $user->phone_number;
        $this->gender        = $user->gender;
        $this->city          = $user->city;
        $this->state_country = $user->state_country;
        $this->email         = $user->email;
        $this->currentAvatar = $user->avatar; // save old filename
        $this->avatar        = null;
    }

    public function updateUser()
    {
        $this->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|unique:users,email,' . $this->user->id,
            'password' => 'nullable|min:8',
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
        ]);

        $user = $this->user->findOrFail($this->user->id);

        $user->first_name    = $this->first_name;
        $user->last_name     = $this->last_name;
        $user->email         = $this->email;
        $user->phone_number  = $this->phone_number;
        $user->gender        = $this->gender;
        $user->city          = $this->city;
        $user->state_country = $this->state_country;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        if ($this->avatar) {
            if ($this->currentAvatar) { // delete old image
                $this->deleteAvatar($this->currentAvatar);
            }

            $user->avatar = $this->saveAvatar($this->avatar); // store new avatar
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
        return view('livewire.admin-profile-form');
    }
}
