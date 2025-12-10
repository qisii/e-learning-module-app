<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private $user;
    const LOCAL_STORAGE_FOLDER = 'avatars/';

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display the user's profile form.
     */
    // public function show(Request $request): View
    // {
    //     return view('users.profile.show', [
    //         'user' => $request->user(),
    //     ]);
    // }
    public function show(Request $request)
    {
        $user = $request->user();

        return view('users.profile.show')->with('user', $user);
        // abort(403, 'Unauthorized action.');
    }

    public function showAdmin(Request $request)
    {
        $user = $request->user();
        
        return view('admin.profile.show')->with('user', $user);

        // abort(403, 'Unauthorized action.');
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }

    public function update(Request $request){
        $user = $this->user->findOrFail(Auth::user()->id);

        $rules = [
            'first_name'    => 'required|max:50',
            'last_name'     => 'required|max:50',
            'phone_number'  => 'nullable',
            'gender'        => 'nullable',
            'city'          => 'nullable',
            'state_country' => 'nullable',
            'password'      => 'nullable|min:8',
            'avatar'        => 'nullable|mimes:jpeg,jpg,png,gif|max:1048',
        ];

        if ($user->role_id === 1) {
            $rules['email']    = 'required|email|max:50|unique:users,email,' . $user->id;
            $rules['username'] = 'nullable|max:20|unique:users,username,' . $user->id;
        } else {
            $rules['username'] = 'required|max:20|unique:users,username,' . $user->id;
            $rules['email']    = 'nullable|email|max:50|unique:users,email,' . $user->id;
        }

        $request->validate($rules);

        $user->first_name       = $request->first_name;
        $user->last_name        = $request->last_name;
        $user->username         = $request->username;
        $user->phone_number     = $request->phone_number;
        $user->gender           = $request->gender;
        $user->city             = $request->city;
        $user->state_country    = $request->state_country;
        $user->email            = $request->email;

        // New Password
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        // Check if there is an avatar
        if($request->avatar){
            if($user->avatar){
                $this->deleteAvatar($user->avatar);
            }
            $user->avatar = $this->saveAvatar($request->avatar);
        }

        $user->save();

        session()->flash('flash.type', 'success');
        session()->flash('flash.message', 'Your profile has been updated successfully!');

        if ($user->role_id == 1) {
            return redirect()->route('admin.profile.show');
        }else{
            return redirect()->route('profile.show');
        }
    }


    private function saveAvatar($avatar){
        $avatar_name = time() . "." . $avatar->extension();
        $avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name);
        return $avatar_name;
    }

    private function deleteAvatar($avatar){
        $avatar_path = self::LOCAL_STORAGE_FOLDER . $avatar;

        if(Storage::disk('public')->exists($avatar_path)){
            Storage::disk('public')->delete($avatar_path);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function destroyAvatar(){
        $user = $this->user->findOrFail(Auth::user()->id);
        $this->deleteAvatar($user->image);

        return redirect()->back();
    }
}
