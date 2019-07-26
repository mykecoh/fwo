<?php
 
namespace App\Http\Controllers\Auth;
 
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

 
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
 
    use AuthenticatesUsers {
    logout as performLogout;
}
 
    public function logout(Request $request)
    {
        // $this->performLogout($request);
        Auth::logout();
        return redirect()->route('login');
    }
    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/Dashboard';

 
    /**
     * Login username to be used by the controller.
     *
     * @var string
     */
    protected $username;
 
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
 
        $this->username = $this->findUsername();

    }
 
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');
 
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
 
        request()->merge([$fieldType => $login]);
 
        return $fieldType;

    }
 
    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;

        Session::put('user_id', Auth::user()->id);
    }


    // public function loginprocess(Request $request){
    //     $username = $request->get('username');
    //     $password = $request->get('password');

    //     $checkuser = User::selectRaw("Count(*) as Total")->where('Username','=',$username)->first();

    //     if(intval($checkuser->Total) > 0){
    //         $getpassword = User::select("Password")->where('Username','=',$username)->first();
    //         if(password_verify($password,$getpassword->Password)){
    //             $request->session()->set('username',$username)
    //         }
    //     }

    // }
}