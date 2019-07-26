<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use App\User3;
use DB;
use App\News;
use App\Pcharacter;

class DashboardController extends Controller
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
    public function index()
    {   
        $title = "Admin Dashboard";
        
        $total_accounts = DB::connection("mysql")->table('users')->where('group','!=','1')->count();
        $total_bans = DB::connection("mysql")->table('users')->where('banned','=','1')->count();
        $total_credits = DB::connection("mysql")->table('users')->where('group','!=','1')->sum('credit');
        $total_ft = DB::connection("mysql")->table('users')->where('group','!=','1')->sum('ft');


        $news = News::orderBy('date', 'desc')->get();

        
        //$pcharacters = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->count();

        return view('Admin/dashboard',compact('title','total_accounts','total_credits','news','total_bans','total_ft'));
    }
}
