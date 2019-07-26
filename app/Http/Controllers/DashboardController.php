<?php

namespace App\Http\Controllers;

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
        $title = "Dashboard";
         //$purchase = Purchase::all();
        
        //$users = DB::select('select * from users');
        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
        //$news = DB::connection("mysql")->table('news');
        //$news = News::get()->orderBy('id','asc');
        $news = DB::connection("mysql")->table('news')->orderBy('id','desc')->get();

        
        $pcharacters = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->count();

        // dd($users);

        //$charInfos = DB::connection("mysql3")->table("pcharacter");
        // $charInfos = DB::connection("mysql")->table('users')
        //     ->where(['users.username' => auth()->user()->username])
        //     ->join('fwworlddevdb.pcharacter as db2','users.username','=','db2.Username')
        //     ->join('fwworlddevdb.charinv_all as db3','charinv_all.CharID','=','db2.CharID')
        //     //->join('item','item.ItemID','=','treasure.item1')
        //     ->get();

        //if($pcharacters != 0){
          $charInfos = DB::connection("mysql3")->table('pcharacter')
              ->select('pcharacter.Username','pcharacter.CharacterName','pcharstats_all.Level','db3.name','pcharstats_all.WaitPeriod')
              ->where(['pcharacter.Username' => auth()->user()->username])
              ->join('DB.users as db2','pcharacter.Username','=','db2.username')
              //->join('fwworlddevdb.charinv_all as db3','db3.CharID','=','db2.id')
              ->join('clan','clan.ClanID','=','pcharstats_all.ClanID')
              ->join('pcharstats_all','pcharstats_all.CharID','=','pcharacter.CharID')
              ->join('DB.clan as db3','pcharstats_all.ClanID','=','db3.id')
              ->get();  
        //}
        

        // dd($charInfos);


        //return view('purchase.index',compact('purchase','title'));
        return view('dashboard',compact('title','users','pcharacters','news','charInfos'));
    }
}
