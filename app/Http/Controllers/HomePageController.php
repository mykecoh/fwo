<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
//use App\Purchase;
use App\User;
use App\Payment;
use App\CreditTransferLog;
use App\User3;
use DB;

class HomePageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function clans()
    {   
         //$title = "Purchase";
         //$purchase = Purchase::all();

         $Clans = DB::connection("mysql3")
         ->table('intdata')
         ->join('clan','clan.ClanID','=','intdata.ClanID')
         ->join('pcharacter','pcharacter.CharID','=','intdata.CharID')
         ->where('job','=','1')
         ->where('intdata.ClanID','!=','0')
         ->where('GuildID','<','1000')->get();

        if(!empty($Clans)){
            foreach($Clans as $clan){
                if($clan->ClanID == 1){
                  $Clan1 = $clan->CharacterName;
                }

                if($clan->ClanID == 2){
                  $Clan2 = $clan->CharacterName;
                }

                if($clan->ClanID == 3){
                  $Clan3 = $clan->CharacterName;
                }

                if($clan->ClanID == 4){
                  $Clan4 = $clan->CharacterName;
                }

                if($clan->ClanID == 5){
                  $Clan5 = $clan->CharacterName;
                }
            }
        }

        

        $Ministers1 = DB::connection("mysql3")
         ->table('intdata')
         ->join('clan','clan.ClanID','=','intdata.ClanID')
         ->join('pcharacter','pcharacter.CharID','=','intdata.CharID')
         ->where('job','=','2')
         ->where('intdata.ClanID','=','1')
         //->where('intdata.ClanID','!=','0')
         ->where('GuildID','<','1000')->get();

         $Ministers2 = DB::connection("mysql3")
         ->table('intdata')
         ->join('clan','clan.ClanID','=','intdata.ClanID')
         ->join('pcharacter','pcharacter.CharID','=','intdata.CharID')
         ->where('job','=','2')
         ->where('intdata.ClanID','=','2')
         //->where('intdata.ClanID','!=','0')
         ->where('GuildID','<','1000')->get();

         $Ministers3 = DB::connection("mysql3")
         ->table('intdata')
         ->join('clan','clan.ClanID','=','intdata.ClanID')
         ->join('pcharacter','pcharacter.CharID','=','intdata.CharID')
         ->where('job','=','2')
         ->where('intdata.ClanID','=','3')
         //->where('intdata.ClanID','!=','0')
         ->where('GuildID','<','1000')->get();

         $Ministers4 = DB::connection("mysql3")
         ->table('intdata')
         ->join('clan','clan.ClanID','=','intdata.ClanID')
         ->join('pcharacter','pcharacter.CharID','=','intdata.CharID')
         ->where('job','=','2')
         ->where('intdata.ClanID','=','4')
         //->where('intdata.ClanID','!=','0')
         ->where('GuildID','<','1000')->get();

         $Ministers5 = DB::connection("mysql3")
         ->table('intdata')
         ->join('clan','clan.ClanID','=','intdata.ClanID')
         ->join('pcharacter','pcharacter.CharID','=','intdata.CharID')
         ->where('job','=','2')
         ->where('intdata.ClanID','=','5')
         //->where('intdata.ClanID','!=','0')
         ->where('GuildID','<','1000')->get();
        
         // dd($Ministers4);

         return view('clans',compact('title','Clans','Clan1','Clan2','Clan3','Clan4','Clan5','Ministers1','Ministers2','Ministers3','Ministers4','Ministers5'));
    }

    public function download(){

        $Downloads = DB::connection("mysql")
        ->table('download')
        ->get();

        return view('download',compact('title','Downloads'));
    }

    public function check(){
        //ssh -o 'StrictHostKeyChecking=no' root@113.23.167.243 \"/usr/local/bin/setcmd 48 1073896916\"
        // $a = exec('whoami');
        // dd($a, 'bbb');

        // $a = passthru("ssh -o 'StrictHostKeyChecking=no' -i /rahsia/id_rsa root@113.23.167.243 \"/usr/local/bin/setcmd 48 1073896916\" 2>&1"); // ni yg ok
        // dd($a, 'bbb', 'ccc');
        // $query = $world->query('SELECT Address FROM pcharacter p, scene s WHERE p.SceneID = s.SceneID AND p.CharID = '.$charid);
        
        $Boosts = DB::connection("mysql")
        ->table('boost_account')
        ->select('boost_account.*','users.username','db2.CharacterName')
        ->join('users','users.id','=','boost_account.uid')
        ->join('fwworlddevdb.pcharacter as db2','boost_account.charid','=','db2.CharID')
        ->where('boost_account.status','!=','1')
        ->get();
        
        date_default_timezone_set("Asia/Kuala_Lumpur");
        for($a = 0; $a < count($Boosts); $a++){
            if($Boosts[$a]->timeexpired < date('Y-m-d H:i:s') && $Boosts[$a]->status == 2){
                passthru("ssh -o 'StrictHostKeyChecking=no' -i /rahsia/id_rsa root@113.23.167.243 \"/usr/local/bin/setcmd 48 ".$Boosts[$a]->charid."\" 2>&1"); // ni yg ok

                DB::connection("mysql")->table('boost_account')->where('charid','=',$Boosts[$a]->charid)->update(['status' => '3']);
                DB::connection("mysql3")->table('pcharstats_all')->where('CharID','=',$Boosts[$a]->charid)->update(['MulPerc' => $Boosts[$a]->orixp]);

                DB::connection("mysql3")->table('authorized')->where('Username','=',$Boosts[$a]->username)->delete();
                DB::connection("mysql3")->table('authenticated')->where('Username','=',$Boosts[$a]->username)->delete();
            }else{
                if($Boosts[$a]->timeexpired > date('Y-m-d H:i:s') && $Boosts[$a]->status == 2){
                    DB::connection("mysql3")->table('pcharstats_all')->where('CharID','=',$Boosts[$a]->charid)->update(['MulPerc' => $Boosts[$a]->boostxp]);
                }
            }
        }
        

        return view('check',compact('title','Boosts'));
    }

    
}
