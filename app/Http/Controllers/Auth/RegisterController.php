<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\UserSecond;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;
use Illuminate\Mail\Mailer;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $randomkey = md5(uniqid(mt_rand(), true));
        User::create([
            'username' => $data["username"],
            'password' => bcrypt($data['password']),
            'email' => $data['email'],
            'name' => $data['name'],
            'group' => '3',
            'date_registered' => date("Y-m-d h:i:sa"),
            'nonce' => $randomkey,
            'status' => '1',
            'credit' => '5000' // buang bila abis closed beta
        ]);

        $salt = substr($data["password"], 0, 2);

        UserSecond::create([
            'Username' => $data["username"],
            'Password' => $data["password"],
            'EPassword' => crypt($data["password"], $salt)
        ]);

   
    $link = url('/verify/'.$randomkey);

    $message = "Hello, Thanks for registering at FwClans Online  .Please click <a href=".$link.">Here</a> to complete email verification. <br />Enjoy your stay here :)<br /><br />Thanks<br />(GM) ";

     \Mail::raw($message, function ($message) use ($data){
            $message->to($data['email'])
            ->subject('FwClans Email Verification');
            $message->from('noreply@FwClans.com', 'FwClans Online :: No Reply');
            $message->sender('noreply@FwClans.com', 'FwClans Online :: No Reply');

    });
    return redirect('/login')->with('success','Please Check Your Email For Verify. Thank You.');
    }

    public function verifyUser($nonce)
         {
             $verifyUser = User::where('nonce', $nonce)->first();
             //dd($verifyUser);
             if(isset($verifyUser) ){
                 $user = $verifyUser;
                 if(!$user->active) {
                     $verifyUser->active = 1;
                     $verifyUser->save();
                     $verifyId = UserSecond::where('Username', $verifyUser->username)->first();
                     $verifyId->SvcLevel = 1;
                     $verifyId->save();
                     $status = "Your e-mail is verified. You can now login.";
                 }else{
                     $status = "Your e-mail is already verified. You can now login.";
                 }
             }else{
                 return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
             }

             return redirect('/login')->with('status', $status);
         }
}
