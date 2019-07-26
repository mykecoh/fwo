<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\User3;
use DB;
use App\News;
use App\Pcharacter;
use App\Profile;
use Hash;
use Auth;

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

        return view('profile',compact('title','profiles'));
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
                    return redirect('profile')->with('unsuccess', 'Password does not match!');
                }

                $user->password =  bcrypt($request->get('new_password'));

                

                if (!(Hash::check($request->get('password'), Auth::user()->password))) {
                    return redirect('profile')->with('unsuccess', "Your current password does not matches with the password you provided. Please try again.");
                }
                

                $salt = substr($request->get('new_password'), 0, 2);
                $epasswd = crypt($request->get('new_password'), $salt);

                //dd($user->password);

                DB::connection("mysql2")->table('subscription')->where('Username','=',auth()->user()->username)->update(['EPassword' => $epasswd]);
            
            
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

            return redirect('profile')->with('success', 'Profile Have Been updated!');  
        }
    }
}
