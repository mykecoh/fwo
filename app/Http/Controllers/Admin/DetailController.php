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
use \resources\views\Admin\Detail;

class DetailController extends Controller
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

    public function playerDetail($id)
    {   
        $title = "Player Character Details";        
        $Characters = DB::connection("mysql3")->table("pcharacter")->where('CharID','=',$id)->first();

        return view('Admin/Detail/playerDetail',compact('title','Characters','MenuCharacter'));
    }

    public function statsDetail($id)
    {   
        $title = "Player Character Stats";        
        $Characters = DB::connection("mysql3")->table("pcharstats_all")->where('CharID','=',$id)->first();

        return view('Admin/Detail/statsDetail',compact('title','Characters','MenuCharacter'));
    }

    public function inventoryDetail($id)
    {   
        $title = "Player Character Inventory";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        $Inventories = DB::connection("mysql3")->table("charinv_all")
        //->join('string','string.id','=','charinv_all.ItemID')
        ->join('DB.string as db2','charinv_all.ItemID','=','db2.id')
        ->where('CharID','=',$id)->orderBy('SlotNum','ASC')->get();
        // dd($Characters);

        $slot_desc=array(
            1=>'MOUSE',
            'Torso',
            'Neck',
            'Shoulder',
            'Arms',
            'Feet',
            'Face',
            'Ring1',
            'Ring2',
            10=>'Weapon',
            26=>'',
            27=>'',
            28=>'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            30=>'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            40=>'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            50=>'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            60=>'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            'QuickSlot',
            86=>'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            90=>'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            100=>'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            110=>'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            120=>'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            130=>'Inventory',
            'Inventory',
            'Inventory',
            'Inventory',
            151=>'Crafting/Trading',
            'Crafting/Trading',
            'Crafting/Trading',
            'Crafting/Trading',
            'Crafting/Trading',
            'Crafting/Trading',
            'Crafting/Trading',

            175=>'Loot',
            'Loot',
            'Loot',
            'Loot',
            'Loot',
            180=>'Loot'

            );

    // dd($slot_desc);

        return view('Admin/Detail/inventoryDetail',compact('title','Characters','Inventories','slot_desc'));
    }

    public function stashDetail($id)
    {   
        $title = "Player Character Stash";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        $Stashs = DB::connection("mysql3")->table("stash_all")
        ->join('DB.string as db2','stash_all.ItemID','=','db2.id')
        ->where('CharID','=',$id)->orderBy('SlotNum','ASC')->get();

        return view('Admin/Detail/stashDetail',compact('title','Characters','Stashs','slot_desc'));
    }

    public function powerDetail($id)
    {   
        $title = "Player Power";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        for($i = 0; $i < 9; $i++){
            if (DB::connection("mysql3")->table('powerlist_'.$i.'')->where('CharID', '=', $id)->count() > 0) {
                    $Powers = DB::connection("mysql3")->table("powerlist_".$i."")
                    ->join('DB.string as db2','powerlist_'.$i.'.PowerID','=','db2.id')
                    ->where('powerlist_'.$i.'.CharID','=',$id)
                    ->orderBy('PowerID','Desc')
                    ->get();
            }  
        }

        return view('Admin/Detail/powerDetail',compact('title','Characters','Powers','slot_desc'));
    }

    public function skillDetail($id)
    {   
        $title = "Player Skills";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        $Skills = DB::connection("mysql3")->table("skilllist_all")
        ->join('DB.string as db2','skilllist_all.SkillID','=','db2.id')
        ->where('CharID','=',$id)
        ->orderBy('SkillID','ASC')->get();
        // dd($Characters);

    // dd($slot_desc);

        return view('Admin/Detail/skillDetail',compact('title','Characters','Skills'));
    }

    public function effectDetail($id)
    {   
        $title = "Player Effect";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        $i = 5;
        for($i=0; $i < 9; $i++){
            if (DB::connection("mysql3")->table('powerlist_'.$i.'')->where('CharID', '=', $id)->count() > 0) {
                $effects = DB::connection("mysql3")->table("effectlist_".$i."")
                ->join('DB.string as db2','effectlist_'.$i.'.EffectID','=','db2.id')
                ->where('CharID','=',$id)
                ->orderBy('EffectID','ASC')->get();
            }
        }

        return view('Admin/Detail/effectDetail',compact('title','Characters','effects'));
    }

    public function stanceDetail($id)
    {   
        $title = "Player Stance";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        
        $stances = DB::connection("mysql3")->table("stancelist_all")
        ->join('DB.string as db2','stancelist_all.StanceID','=','db2.id')
        ->where('CharID','=',$id)
        ->orderBy('Rank','Desc')->get();
            

        return view('Admin/Detail/stanceDetail',compact('title','Characters','stances'));
    }

    public function questDetail($id)
    {   
        $title = "Player Quest";        
        $Characters = DB::connection("mysql3")->table("charinv_all")->where('CharID','=',$id)->first();

        
        $quests = DB::connection("mysql3")->table("pcharacter")
        //->join('string','string.id','=','stancelist_all.StanceID')
        ->where('CharID','=',$id)
        //->orderBy('Rank','Desc')
        ->first();
// 

        dd($quests->QuestData);

        return view('Admin/Detail/questDetail',compact('title','Characters','quests'));
    }

    function U16btoU8str($str)
    {
        //FEFF big-endian - direct
        //FFFE little-endian - need swap

        $len = strlen($str);
        $out = NULL;
        for($idx = 0; $idx < $len; $idx+=2)
        {
            $out .= $this->U16toU8($str[$idx+1], $str[$idx]);
        }
        return $out;
    }

    function U16toU8($st, $nd)
    {
        $v = (ord($st) * 256) + ord($nd);
        if($v <= 127)
        {
            return pack('c', $v);
        }
        elseif($v <= 2047)
        {
            return pack('C*', 192 | ($v >> 6), 128 | ($v & 63));
        }
        elseif($v <= 65535)
        {
            return pack('C*', 224 | ($v >> 12), 128 | ($v >> 6) & 63, 128 | ($v & 63));
        }
        else
        {
            die('invalid');
        }
    }
}
