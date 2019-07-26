<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Profile;
use App\User3;
use DB;
use App\News;
use App\Pcharacter;
use App\User;
use Auth;
use Hash;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function profile()
    {   
        $title = "Profile";        
        $profiles = DB::connection("mysql")->table("users")->where('Username','=',auth()->user()->username)->first();

        return view('Admin/profile',compact('title','profiles'));
    }

    public function updateProfile(Request $request, $id)
    {    
        $this->validate($request, [
            'email' => 'required',
            'username'=>'required',
            //'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]); 
            
            $user = Profile::find($id);

            if(empty($request->get('password')) || $request->get('password') == "NULL"){
                // kalau x ubah password xde effect
            }else{
                if($request->get('new_password') != $request->get('repeat_new_password')){
                    return redirect('Admin/profile')->with('unsuccess', 'Password does not match!');
                }

                if (!(Hash::check($request->get('password'), Auth::user()->password))) {
                    return redirect('Admin/profile')->with('unsuccess', "Your current password does not matches with the password you provided. Please try again.");
                }
                
                $user->password =  bcrypt($request->get('new_password'));

                $salt = substr($user->password, 0, 2);
                $epasswd = crypt($user->password, $salt);

                DB::connection("mysql2")->table('subscription')->where('Username','=',auth()->user()->username)->update(['EPassword' => $epasswd]);
            }
            
            $user->name =  $request->get('name');
            $user->gender = $request->get('gender');
            $user->dob = $request->get('dob');
            $user->phone = $request->get('phone');
            $user->facebook = $request->get('facebook');
            $user->website = $request->get('website');
            $user->twitter = $request->get('twitter');
            $user->yahoo = $request->get('yahoo');
            $user->skype = $request->get('skype');

            $user->save();

            return redirect('Admin/profile')->with('success', 'Profile Have Been updated!');  

    }
}
