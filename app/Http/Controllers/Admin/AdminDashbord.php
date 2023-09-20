<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminDashbord extends Controller
{
    public function index(){
        return view("Admin.main");
    }

    public function profile(){
        return view('user-profile.user-profile');
    }

    public function edite(){
        return view("user-profile.edite-profile");
    }

    public function store(Request $request)
    {
        // dd($request->input('check'));
        // dd($request->file('avatar'));
        // dd(Auth::user()->id);
        // dd($user = User::findOrFail(Auth::user()->id));
        // Get the authenticated user
        $user = auth()->user();
        // // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required',
            'bithdate' => 'required|date',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:5048',
            'email' => ['email',Rule::unique('users')->ignore($user->email, 'email')],
            'adriss' => 'required',
            'city' => 'required',
            'contry' => 'required',
            'pinecode' => 'required',
            'phone' => 'required',
            'password' => 'confirmed',
        ]);

        

        // // Update the user's data
        $user->name = $request->input('name');
        $user->bithdate = $request->input('bithdate');
        $user->email = $request->input('email');
        $user->adriss = $request->input('adriss');
        $user->city = $request->input('city');
        $user->contry = $request->input('contry');
        $user->pinecode = $request->input('pinecode');
        $user->phone = $request->input('phone');

        // // Update the password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Handle the avatar image upload
        if ($request->hasFile('avatar')){
            // dd(auth()->user()->avatar);
            // Delete image :
            Storage::delete('public/'.auth()->user()->avatar);
            // Get the file : img
            $file = $request->file("avatar");
            // Generate name : 
            $name = "avatar_".Carbon::now()->timestamp."_".auth()->user()->name."_".auth()->user()->id.".".$file->extension();
            // Store img in public file
            Storage::putFileAs('public/avatars',$file,$name);
            // Store path in database 
            $user->avatar = 'avatars/'.$name;

        }else if($request->input('check') === "No change"){
            $user->avatar = auth()->user()->avatar;
        }
        else{
            Storage::delete('public/'.auth()->user()->avatar);
            $user->avatar = '';
        }

        // Save the updated user data
        $user->save();

        // // Redirect or return a response as needed
        return redirect()->route("admin.profile")->with('success', 'Profile updated successfully');
    }
}
