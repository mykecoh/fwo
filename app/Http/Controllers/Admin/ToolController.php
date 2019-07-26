<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Item;
use App\ItemCategory;
use App\Boost;
use App\User;
use App\Npcattrib;
use App\Treasure;
use App\Effect;
use DB;
use App\News;
use App\Download;
use App\UniqueItem;
use Intervention\Image\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Session;
use Redirect;
//use App\Purchase;

class ToolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function player(Request $request)
    {   
        $title = "Search Player";
        if($request->submit_btn == 'submit')
        {
            
            $GLOBALS['__crc32_table']=array();        // Lookup table array
            $this->__crc32_init_table();

            $charactername_u16 = $this->U8toU16(trim(stripslashes(request('Username'))));
            $charactername_u16 = substr_replace(str_repeat("\0", 38), $charactername_u16, 0, strlen($charactername_u16));
            $hashnick = sprintf("%u", $this->__crc32_string($charactername_u16));
            $charnm = '0x'. $this->hexstring($charactername_u16) . '0000';


             $players = DB::connection("mysql3")->table("pcharacter")
             ->where('Username','=',request('Username'))
             ->orWhere('CharID','=',request('Username'))
             ->orWhere('CharacterName','LIKE','%'.$charactername_u16.'%')
             ->get();

        }
        else
        {
            // $npcs = DB::connection("mysql3")->table("npcattrib")->where('AttribID','=','abcd')->get();
        }

        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Admin/Tool/player',compact('title','usernames','players'));
    }

    function hexstring($str)
    {
        $hex="";
        $len=strlen($str);
        for($n=0;$n<$len;$n++)
        {
            $hex.=str_pad(dechex(ord($str[$n])),2,'0',STR_PAD_LEFT);
        }
        return $hex;
    }

    function U8toU16($str)
    {
        $len = strlen($str);
        $out = NULL;
        $u16 = NULL;
        for($idx = 0; $idx < $len; $idx++)
        {
            if((ord($str[$idx]) & 128) == 0)
            {
                $u16 = pack('v', ord($str[$idx]));
            }
            elseif((ord($str[$idx]) & 224) == 192)
            {
                $u16 = pack('v', ((31 & ord($str[$idx])) << 5) | (63 & ord($str[$idx+1])));
                $idx+=1;
            }
            elseif((ord($str[$idx]) & 240) == 224)
            {
                $u16 = pack('v', (((15 & ord($str[$idx])) << 12) | ((63 & ord($str[$idx+1])) << 6) | (63 & ord($str[$idx+2]))));
                $idx+=2;
            }
            $out .= $u16;
        }
        return $out;
    }

    function __crc32_string($text) {        // Creates a CRC from a text string
        // Once the lookup table has been filled in by the two functions above,
        // this function creates all CRCs using only the lookup table.

        // You need unsigned variables because negative values
        // introduce high bits where zero bits are required.
        // PHP doesn't have unsigned integers:
        // I've solved this problem by doing a '&' after a '>>'.

        // Start out with all bits set high.
        $crc=0xffffffff;
        $len=strlen($text);

        // Perform the algorithm on each character in the string,
        // using the lookup table values.
        for($i=0;$i < $len;++$i) {
            $crc=(($crc >> 8) & 0x00ffffff) ^ $GLOBALS['__crc32_table'][($crc & 0xFF) ^ ord($text{$i})];
        }

        return $crc; //PGS style :)

        // Exclusive OR the result with the beginning value.
        return $crc ^ 0xffffffff;
    }

    function __crc32_init_table() {     
            $polynomial = 0x04c11db7;
    
            for($i=0;$i <= 0xFF;++$i) {
                    $GLOBALS['__crc32_table'][$i]=($this->__crc32_reflect($i,8) << 24);
                    for($j=0;$j < 8;++$j) {
                            $GLOBALS['__crc32_table'][$i]=(($GLOBALS['__crc32_table'][$i] << 1) ^
                            (($GLOBALS['__crc32_table'][$i] & (1 << 31))?$polynomial:0));
                    }
                    $GLOBALS['__crc32_table'][$i] = $this->__crc32_reflect($GLOBALS['__crc32_table'][$i], 32);
            }
    }

    function __crc32_reflect($ref, $ch) {      
            $value=0;
            for($i=1;$i<($ch+1);++$i) {
                    if($ref & 1) $value |= (1 << ($ch-$i));
                    $ref = (($ref >> 1) & 0x7fffffff);
            }
            return $value;
    }

    public function npc(Request $request)
    {   
        $title = "Player ID";

        if($request->AttribID == "")
        {
            
        }
        elseif($request->submit_btn == 'submit')
        {
             $npcs = DB::connection("mysql3")->table("npcattrib")->where('AttribID','LIKE','%'.request('AttribID').'%')->get();
        }
        else
        {
            $npcs = DB::connection("mysql3")->table("npcattrib")->where('AttribID','=','abcd')->get();
        }

        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Admin/Tool/npc',compact('title','usernames','npcs'));
    }

    public function npcDetail(Request $request, $id)
    {   
        $title = "Monster Detail";

        $npcinfo = DB::connection("mysql3")->table("npcattrib")->where('AttribID','=',$id)->first();

        $iteminfos = DB::connection("mysql3")->table('treasure')
            ->where(['treasure.TableID' => $id])
            ->join('DB.string as db2','treasure.item1','=','db2.id')
            ->join('item','item.ItemID','=','treasure.item1')
            ->groupBy('treasure.Indx')
            ->get();

        $addIndex = DB::connection("mysql3")->table('treasure')
            ->where(['treasure.TableID' => $id])
            ->orderBy('IndexID','desc')
            ->first();

        
        if($request->submit_btn == "")
        {
            
        }
        elseif($request->submit_btn == 'submit')
        {
            $treas = new Treasure();

            $treas->TableID = request('TableID');
            $treas->Item1 = request('item_id');
            $treas->IndexID = $addIndex->IndexID+1;
            $treas->Quantity1 = request('quantity');
            $treas->GoldMin = request('gold_min');
            $treas->Rarity = request('rarity');
            $treas->GoldMax = request('gold_max');
            $treas->Durability1 = request('durability');


            $treas->save();

            return Redirect::back()->with('success', 'Data Saved!');
        }else{

        }

        return view('Admin/Tool/npcDetail',compact('title','usernames','npcinfo','iteminfos'));
    }

    public function editTreasure($id){
         $title = "Edit Treasure";

        $iteminfos = DB::connection("mysql3")->table('treasure')
                    ->where(['treasure.Indx' => $id])
                    ->first();


        return view('Admin/Tool/editTreasure',compact('title','iteminfos'));
    }

    public function updateTreasure(Request $request, $id){

            //dd(request('TableID'));

            $treas = Treasure::where('Indx', '=', $id)->first();

            $treas->TableID = request('TableID');
            $treas->Item1 = request('item_id');
            $treas->Quantity1 = request('quantity');
            $treas->GoldMin = request('gold_min');
            $treas->Rarity = request('rarity');
            $treas->GoldMax = request('gold_max');
            $treas->Durability1 = request('durability');


            $treas->save();

            return redirect('Admin/Tool/npcDetail/'.$treas->TableID)->with('success', 'Treasure updated!');
    }

    public function editNpcDetail($id)
    {   
        $title = "Edit Monster Detail";

        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();
        $npcinfo = DB::connection("mysql3")->table("npcattrib")->where('AttribID','=',$id)->first();

        return view('Admin/Tool/editNpcDetail',compact('title','usernames','npcinfo'));
    }


    public function trap()
    {   
        $title = "Trap";

        $traps1 = DB::connection("mysql3")->table('clanupgrades')
        ->select('clanupgrades.*','npcattrib.AttribID','npcattrib.PowerRank1','npcattrib.PowerID1','npcattrib.PowerID1','powers.Duration1','powers.CoolDown','powers.EffectID1','effects.*')
        ->join('npcattrib','npcattrib.AttribID','=','clanupgrades.Field1')
        ->join('powers','powers.PowerID','=','npcattrib.PowerID1')
        ->join('effects','effects.EffectID','=','powers.EffectID1')
        //->where('CharID','=','1073896920')
        ->where('Field2','=','1')
        ->whereBetween('Field1', array(768, 851))->get();

        // dd($traps1);

        $traps2 = DB::connection("mysql3")->table('clanupgrades')
        ->select('clanupgrades.*','npcattrib.AttribID','npcattrib.PowerRank1','npcattrib.PowerID1','npcattrib.PowerID1','powers.Duration1','powers.CoolDown','powers.EffectID1','effects.*')
        ->join('npcattrib','npcattrib.AttribID','=','clanupgrades.Field1')
        ->join('powers','powers.PowerID','=','npcattrib.PowerID1')
        ->join('effects','effects.EffectID','=','powers.EffectID1')
        //->where('CharID','=','1073896920')
        ->where('Field2','=','2')
        ->whereBetween('Field1', array(768, 851))->get();

        $traps3 = DB::connection("mysql3")->table('clanupgrades')
        ->select('clanupgrades.*','npcattrib.AttribID','npcattrib.PowerRank1','npcattrib.PowerID1','npcattrib.PowerID1','powers.Duration1','powers.CoolDown','powers.EffectID1','effects.*')
        ->join('npcattrib','npcattrib.AttribID','=','clanupgrades.Field1')
        ->join('powers','powers.PowerID','=','npcattrib.PowerID1')
        ->join('effects','effects.EffectID','=','powers.EffectID1')
        //->where('CharID','=','1073896920')
        ->where('Field2','=','3')
        ->whereBetween('Field1', array(768, 851))->get();

        $traps4 = DB::connection("mysql3")->table('clanupgrades')
        ->select('clanupgrades.*','npcattrib.AttribID','npcattrib.PowerRank1','npcattrib.PowerID1','npcattrib.PowerID1','powers.Duration1','powers.CoolDown','powers.EffectID1','effects.*')
        ->join('npcattrib','npcattrib.AttribID','=','clanupgrades.Field1')
        ->join('powers','powers.PowerID','=','npcattrib.PowerID1')
        ->join('effects','effects.EffectID','=','powers.EffectID1')
        //->where('CharID','=','1073896920')
        ->where('Field2','=','4')
        ->whereBetween('Field1', array(768, 851))->get();

        $traps5 = DB::connection("mysql3")->table('clanupgrades')
        ->select('clanupgrades.*','npcattrib.AttribID','npcattrib.PowerRank1','npcattrib.PowerID1','npcattrib.PowerID1','powers.Duration1','powers.CoolDown','powers.EffectID1','effects.*')
        ->join('npcattrib','npcattrib.AttribID','=','clanupgrades.Field1')
        ->join('powers','powers.PowerID','=','npcattrib.PowerID1')
        ->join('effects','effects.EffectID','=','powers.EffectID1')
        //->where('CharID','=','1073896920')
        ->where('Field2','=','5')
        ->whereBetween('Field1', array(768, 851))->get();



        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Admin/Tool/trap',compact('title','usernames','traps1','traps2','traps3','traps4','traps5'));
    }


     public function relic()
    {   
        $title = "Relic";

        $relics = DB::connection("mysql3")->table('uniqueitem')
        //->join('string','string.id','=','uniqueitem.ItemID')
        ->join('DB.string as db3','uniqueitem.ItemID','=','db3.id')
        ->get();

        return view('Admin/Tool/relic',compact('title','relics'));
    }


    public function effect(Request $request)
    {   
        $title = "Effect";

        if($request->id == "")
        {
            
        }
        elseif($request->submit_btn == 'submit')
        {
             $effects = DB::connection("mysql3")->table('effects')
            ->where('EffectID','=',request('id'))
            ->get();
        }else{

        }

        
        return view('Admin/Tool/effect',compact('title','effects'));
    }

    public function effectDetail($id)
    {
        $title = "Effect";

        $itemdetails = DB::connection("mysql3")->table('item')
            ->join('DB.string as db3','item.itemid','=','db3.id')
            //->join('string','string.id','=','item.ItemID')
            ->orWhere('Field4','=',$id)
            ->orWhere('Field5','=',$id)
            ->orWhere('Field6','=',$id)
            ->get();


        $effectinfo = DB::connection("mysql3")->table('effects')
            ->where('EffectID','=',$id)
            ->first();



        return view('Admin/Tool/effectDetail',compact('title','effectinfo','itemdetails'));
    }

    public function relicUpdate(Request $request){

        $query = DB::connection("mysql3")->table('uniqueitem');

        for($i=0; $i<count($request->ItemID); $i++) {
            UniqueItem::where(['ItemID' => $request->ItemID[$i]])->update(['CharID' => $request->CharID[$i]]);
        }

        //UniqueItem::whereIn('ItemID', request('ItemID'))->update(['CharID' => request('CharID'), 'DecayCounter' => request('DecayCounter')]);

        return Redirect::back()->with('success', 'Unique Updated!');
    }


    public function updateNpcDetail(Request $request, $id){

        $this->validate($request, [
            'ModelID' => 'required',
            'AttachmentID'=>'required',
        ]);

        $npcattribs = Npcattrib::where('AttribID', '=', $id)->first();
        
        $npcattribs->AttribID =  $request->get('AttribID');
        $npcattribs->ModelID =  $request->get('ModelID');
        $npcattribs->AttachmentID =  $request->get('AttachmentID');
        $npcattribs->AttRating =  $request->get('AttRating');
        $npcattribs->DefRating =  $request->get('DefRating');
        $npcattribs->MinFireDmg =  $request->get('MinFireDmg');
        $npcattribs->MaxFireDmg =  $request->get('MaxFireDmg');
        $npcattribs->MinColdDmg =  $request->get('MinColdDmg');
        $npcattribs->MaxColdDmg =  $request->get('MaxColdDmg');
        $npcattribs->MinLightningDmg =  $request->get('MinLightningDmg');
        $npcattribs->MaxLightningDmg =  $request->get('MaxLightningDmg');
        $npcattribs->MinPoisonDmg =  $request->get('MinPoisonDmg');
        $npcattribs->MaxPoisonDmg =  $request->get('MaxPoisonDmg');
        $npcattribs->MinPhysicalDmg =  $request->get('MinPhysicalDmg');
        $npcattribs->MaxPhysicalDmg =  $request->get('MaxPhysicalDmg');
        $npcattribs->MaxHitPoints =  $request->get('MaxHitPoints');
        $npcattribs->HitPointRegen =  $request->get('HitPointRegen');
        $npcattribs->FireResist =  $request->get('FireResist');
        $npcattribs->ColdResist =  $request->get('ColdResist');
        $npcattribs->LightningResist =  $request->get('LightningResist');
        $npcattribs->PoisonResist =  $request->get('PoisonResist');
        $npcattribs->PhysicalResist =  $request->get('PhysicalResist');
        $npcattribs->MoveRate =  $request->get('MoveRate');
        $npcattribs->XPperHP =  $request->get('XPperHP');
        $npcattribs->XPValue =  $request->get('XPValue');
        $npcattribs->Level =  $request->get('Level');
        $npcattribs->Clan =  $request->get('Clan');
        $npcattribs->StanceID =  $request->get('StanceID');
        $npcattribs->AnimStanceID =  $request->get('AnimStanceID');
        $npcattribs->PowerID1 =  $request->get('PowerID1');
        $npcattribs->PowerRank1 =  $request->get('PowerRank1');
        $npcattribs->PowerID2 =  $request->get('PowerID2');
        $npcattribs->PowerRank2 =  $request->get('PowerRank2');
        $npcattribs->PowerID3 =  $request->get('PowerID3');
        $npcattribs->PowerRank3 =  $request->get('PowerRank3');
        $npcattribs->TreasureTableID =  $request->get('TreasureTableID');
        $npcattribs->Invisible =  $request->get('Invisible');
        $npcattribs->AggressiveFlag =  $request->get('AggressiveFlag');
        $npcattribs->AggroValue =  $request->get('AggroValue');
        $npcattribs->ScanOption =  $request->get('ScanOption');
        $npcattribs->PowerMultiplier =  $request->get('PowerMultiplier');
        $npcattribs->MeleeMultiplier =  $request->get('MeleeMultiplier');
        $npcattribs->RangeMultiplier =  $request->get('RangeMultiplier');
        $npcattribs->RetreatHitPoints =  $request->get('RetreatHitPoints');
        $npcattribs->ReturnFlag =  $request->get('ReturnFlag');
        $npcattribs->ChallengeLevel =  $request->get('ChallengeLevel');
        $npcattribs->ScriptID =  $request->get('ScriptID');
        $npcattribs->NameID =  $request->get('NameID');
        $npcattribs->TargetType =  $request->get('TargetType');
        $npcattribs->MeleePerc =  $request->get('MeleePerc');
        $npcattribs->PowerPerc1 =  $request->get('PowerPerc1');
        $npcattribs->PowerPerc2 =  $request->get('PowerPerc2');
        $npcattribs->PowerPerc3 =  $request->get('PowerPerc3');
        $npcattribs->IsGuard =  $request->get('IsGuard');
        $npcattribs->HalfMoveRate =  $request->get('HalfMoveRate');
        $npcattribs->MeleeRange =  $request->get('MeleeRange');
        $npcattribs->PowerRange1 =  $request->get('PowerRange1');
        $npcattribs->PowerRange2 =  $request->get('PowerRange2');
        $npcattribs->PowerRange3 =  $request->get('PowerRange3');
        $npcattribs->EffectID =  $request->get('EffectID');
        $npcattribs->ScanAreaRange =  $request->get('ScanAreaRange');
        $npcattribs->DoClanRating =  $request->get('DoClanRating');
        $npcattribs->EnemyClan =  $request->get('EnemyClan');
        $npcattribs->UpValue =  $request->get('UpValue');
        $npcattribs->DownValue =  $request->get('DownValue');
        $npcattribs->IsSNPC =  $request->get('IsSNPC');
        $npcattribs->InvisiblePerc =  $request->get('InvisiblePerc');
        $npcattribs->MaxItemCount =  $request->get('MaxItemCount');
        $npcattribs->PermanentDeath =  $request->get('PermanentDeath');
        $npcattribs->IsDead =  $request->get('IsDead');
        $npcattribs->WeaponSpeed =  $request->get('WeaponSpeed');
        $npcattribs->DefEffID1 =  $request->get('DefEffID1');
        $npcattribs->DefPowerRank1 =  $request->get('DefPowerRank1');
        $npcattribs->DefDuration1 =  $request->get('DefDuration1');
        $npcattribs->DefEffID2 =  $request->get('DefEffID2');
        $npcattribs->DefPowerRank2 =  $request->get('DefPowerRank2');
        $npcattribs->DefDuration2 =  $request->get('DefDuration2');
        $npcattribs->DefEffID3 =  $request->get('DefEffID3');
        $npcattribs->DefPowerRank3 =  $request->get('DefPowerRank3');
        $npcattribs->DefDuration3 =  $request->get('DefDuration3');
        $npcattribs->DefEffID4 =  $request->get('DefEffID4');
        $npcattribs->DefPowerRank4 =  $request->get('DefPowerRank4');
        $npcattribs->DefDuration4 =  $request->get('DefDuration4');
        $npcattribs->DefEffID5 =  $request->get('DefEffID5');
        $npcattribs->DefPowerRank5 =  $request->get('DefPowerRank5');
        $npcattribs->DefDuration5 =  $request->get('DefDuration5');
        $npcattribs->DefEffID6 =  $request->get('DefEffID6');
        $npcattribs->DefPowerRank6 =  $request->get('DefPowerRank6');
        $npcattribs->DefDuration6 =  $request->get('DefDuration6');
        $npcattribs->DefEffID7 =  $request->get('DefEffID7');
        $npcattribs->DefPowerRank7 =  $request->get('DefPowerRank7');
        $npcattribs->DefDuration7 =  $request->get('DefDuration7');
        $npcattribs->DefEffID8 =  $request->get('DefEffID8');
        $npcattribs->DefPowerRank8 =  $request->get('DefPowerRank8');
        $npcattribs->DefDuration8 =  $request->get('DefDuration8');
        $npcattribs->DefEffID9 =  $request->get('DefEffID9');
        $npcattribs->DefPowerRank9 =  $request->get('DefPowerRank9');
        $npcattribs->DefDuration9 =  $request->get('DefDuration9');
        $npcattribs->DefEffID10 =  $request->get('DefEffID10');
        $npcattribs->DefPowerRank10 =  $request->get('DefPowerRank10');
        $npcattribs->DefDuration10 =  $request->get('DefDuration10');
        $npcattribs->StunResist =  $request->get('StunResist');
        $npcattribs->SlowResist =  $request->get('SlowResist');
        $npcattribs->EntangledResist =  $request->get('EntangledResist');
        $npcattribs->AggroRate =  $request->get('AggroRate');
        $npcattribs->DetectHiddenRate =  $request->get('DetectHiddenRate');
        $npcattribs->CallHelpRatio =  $request->get('CallHelpRatio');
        $npcattribs->CombatTimeout =  $request->get('CombatTimeout');
        $npcattribs->SplitChance =  $request->get('SplitChance');
        $npcattribs->SplitDamageWeakness =  $request->get('SplitDamageWeakness');
        $npcattribs->SplitMax =  $request->get('SplitMax');
        $npcattribs->SplitID =  $request->get('SplitID');
        $npcattribs->SplitFX =  $request->get('SplitFX');
        $npcattribs->SpawnChildChance =  $request->get('SpawnChildChance');
        $npcattribs->SpawnChildID =  $request->get('SpawnChildID');
        $npcattribs->SpawnChildAnim =  $request->get('SpawnChildAnim');
        $npcattribs->BossID =  $request->get('BossID');
        $npcattribs->BossFXID =  $request->get('BossFXID');
        $npcattribs->ScatterChance =  $request->get('ScatterChance');
        $npcattribs->ScatterDamageWeakness =  $request->get('ScatterDamageWeakness');
        $npcattribs->ScatterMax =  $request->get('ScatterMax');
        $npcattribs->ScatterID =  $request->get('ScatterID');
        $npcattribs->ScatterFXID =  $request->get('ScatterFXID');
        $npcattribs->EaterThreshold =  $request->get('EaterThreshold');
        $npcattribs->EaterChance =  $request->get('EaterChance');
        $npcattribs->EaterRange =  $request->get('EaterRange');
        $npcattribs->EaterID =  $request->get('EaterID');
        $npcattribs->EaterHPGain =  $request->get('EaterHPGain');
        $npcattribs->EaterAnim =  $request->get('EaterAnim');
        $npcattribs->MinDirectDmg =  $request->get('MinDirectDmg');
        $npcattribs->MaxDirectDmg =  $request->get('MaxDirectDmg');
        $npcattribs->PowerID4 =  $request->get('PowerID4');
        $npcattribs->PowerID5 =  $request->get('PowerID5');
        $npcattribs->PowerID6 =  $request->get('PowerID6');
        $npcattribs->PowerID7 =  $request->get('PowerID7');
        $npcattribs->PowerID8 =  $request->get('PowerID8');
        $npcattribs->PowerID9 =  $request->get('PowerID9');
        $npcattribs->PowerID10 =  $request->get('PowerID10');
        $npcattribs->PowerRank4 =  $request->get('PowerRank4');
        $npcattribs->PowerRank5 =  $request->get('PowerRank5');
        $npcattribs->PowerRank6 =  $request->get('PowerRank6');
        $npcattribs->PowerRank7 =  $request->get('PowerRank7');
        $npcattribs->PowerRank8 =  $request->get('PowerRank8');
        $npcattribs->PowerRank9 =  $request->get('PowerRank9');
        $npcattribs->PowerRank10 =  $request->get('PowerRank10');
        $npcattribs->PowerPerc4 =  $request->get('PowerPerc4');
        $npcattribs->PowerPerc5 =  $request->get('PowerPerc5');
        $npcattribs->PowerPerc6 =  $request->get('PowerPerc6');
        $npcattribs->PowerPerc7 =  $request->get('PowerPerc7');
        $npcattribs->PowerPerc8 =  $request->get('PowerPerc8');
        $npcattribs->PowerPerc9 =  $request->get('PowerPerc9');
        $npcattribs->PowerPerc10 =  $request->get('PowerPerc10');
        $npcattribs->PowerRange4 =  $request->get('PowerRange4');
        $npcattribs->PowerRange5 =  $request->get('PowerRange5');
        $npcattribs->PowerRange6 =  $request->get('PowerRange6');
        $npcattribs->PowerRange7 =  $request->get('PowerRange7');
        $npcattribs->PowerRange8 =  $request->get('PowerRange8');
        $npcattribs->PowerRange9 =  $request->get('PowerRange9');
        $npcattribs->PowerRange10 =  $request->get('PowerRange10');
        $npcattribs->PowerType1 =  $request->get('PowerType1');
        $npcattribs->PowerType2 =  $request->get('PowerType2');
        $npcattribs->PowerType3 =  $request->get('PowerType3');
        $npcattribs->PowerType4 =  $request->get('PowerType4');
        $npcattribs->PowerType5 =  $request->get('PowerType5');
        $npcattribs->PowerType6 =  $request->get('PowerType6');
        $npcattribs->PowerType7 =  $request->get('PowerType7');
        $npcattribs->PowerType8 =  $request->get('PowerType8');
        $npcattribs->PowerType9 =  $request->get('PowerType9');
        $npcattribs->PowerType10 =  $request->get('PowerType10');
        $npcattribs->ScanScriptID =  $request->get('ScanScriptID');
        $npcattribs->CheckDelete =  $request->get('CheckDelete');
        $npcattribs->InvisibilityPerc =  $request->get('InvisibilityPerc');
        $npcattribs->CheckNPCScriptID =  $request->get('CheckNPCScriptID');
        $npcattribs->MoveAwayPerc =  $request->get('MoveAwayPerc');
        $npcattribs->Lure =  $request->get('Lure');
        $npcattribs->Swarm =  $request->get('Swarm');
        $npcattribs->CallForHelpID =  $request->get('CallForHelpID');
        $npcattribs->Persistent =  $request->get('Persistent');
        $npcattribs->TargetLostID =  $request->get('TargetLostID');
        $npcattribs->ArrivedID =  $request->get('ArrivedID');
        $npcattribs->BodyGuard =  $request->get('BodyGuard');
        $npcattribs->AOEHunter =  $request->get('AOEHunter');
        $npcattribs->AttackScriptID =  $request->get('AttackScriptID');
        $npcattribs->StuckScriptID =  $request->get('StuckScriptID');
        $npcattribs->NPCCheckOW =  $request->get('NPCCheckOW');
        $npcattribs->CallForHelpOW =  $request->get('CallForHelpOW');
        $npcattribs->RangeCheckID =  $request->get('RangeCheckID');
        $npcattribs->RangeCheckOW =  $request->get('RangeCheckOW');
        $npcattribs->AttackScriptOW =  $request->get('AttackScriptOW');
        $npcattribs->PowerScriptID =  $request->get('PowerScriptID');
        $npcattribs->PowerScriptOW =  $request->get('PowerScriptOW');
        $npcattribs->Properties =  $request->get('Properties');
        $npcattribs->IdleTime =  $request->get('IdleTime');
        $npcattribs->BreakSprint =  $request->get('BreakSprint');
        $npcattribs->HitFilter =  $request->get('HitFilter');
        $npcattribs->Rotate =  $request->get('Rotate');
        $npcattribs->LureRadius =  $request->get('LureRadius');

        $npcattribs->save();

        return Redirect::back()->with('success', 'Npc Detail Updated!');

    }

    public function updateEffectDetail(Request $request, $id){
        $effects = Effect::where('EffectID', '=', $id)->first();


        $effects->EffectID =  $request->get('EffectID');
        $effects->Stun = $request->get("Stun");
        $effects->Slow = $request->get("Slow");
        $effects->ImmunityID = $request->get("ImmunityID");
        $effects->RemoveImmunityID = $request->get("RemoveImmunityID");
        $effects->ConstFireDmg = $request->get("ConstFireDmg");
        $effects->ConstColdDmg = $request->get("ConstColdDmg");
        $effects->ConstPoisonDmg = $request->get("ConstPoisonDmg");
        $effects->ConstLightningDmg = $request->get("ConstLightningDmg");
        $effects->ConstPhysicalDmg = $request->get("ConstPhysicalDmg");
        $effects->ConstChiDmg = $request->get("ConstChiDmg");
        $effects->AttackPlus = $request->get("AttackPlus");
        $effects->AttackPerc = $request->get("AttackPerc");
        $effects->DefensePlus = $request->get("DefensePlus");
        $effects->DefensePerc = $request->get("DefensePerc");
        $effects->MinPhysicalDmgPlus = $request->get("MinPhysicalDmgPlus");
        $effects->MaxPhysicalDmgPlus = $request->get("MaxPhysicalDmgPlus");
        $effects->MinFireDmgPlus = $request->get("MinFireDmgPlus");
        $effects->MaxFireDmgPlus = $request->get("MaxFireDmgPlus");
        $effects->MinColdDmgPlus = $request->get("MinColdDmgPlus");
        $effects->MaxColdDmgPlus = $request->get("MaxColdDmgPlus");
        $effects->MinPoisonDmgPlus = $request->get("MinPoisonDmgPlus");
        $effects->MaxPoisonDmgPlus = $request->get("MaxPoisonDmgPlus");
        $effects->MinLightningDmgPlus = $request->get("MinLightningDmgPlus");
        $effects->MaxLightningDmgPlus = $request->get("MaxLightningDmgPlus");
        $effects->FireResistPlus = $request->get("FireResistPlus");
        $effects->ColdResistPlus = $request->get("ColdResistPlus");
        $effects->PoisonResistPlus = $request->get("PoisonResistPlus");
        $effects->LightningResistPlus = $request->get("LightningResistPlus");
        $effects->PhysicalResistPlus = $request->get("PhysicalResistPlus");
        $effects->HitPointPlus = $request->get("HitPointPlus");
        $effects->HitPointPerc = $request->get("HitPointPerc");
        $effects->ChiPlus = $request->get("ChiPlus");
        $effects->ChiPerc = $request->get("ChiPerc");
        $effects->HitPointsRegenPlus = $request->get("HitPointsRegenPlus");
        $effects->ChiPointsRegenPlus = $request->get("ChiPointsRegenPlus");
        $effects->MaxHitPointsPlus = $request->get("MaxHitPointsPlus");
        $effects->MaxHitPointsPerc = $request->get("MaxHitPointsPerc");
        $effects->MaxChiPlus = $request->get("MaxChiPlus");
        $effects->MaxChiPerc = $request->get("MaxChiPerc");
        $effects->WeightPlus = $request->get("WeightPlus");
        $effects->WeightPerc = $request->get("WeightPerc");
        $effects->BlockChangePlus = $request->get("BlockChangePlus");
        $effects->StrengthPlus = $request->get("StrengthPlus");
        $effects->ConstitutionPLus = $request->get("ConstitutionPLus");
        $effects->AgilityPlus = $request->get("AgilityPlus");
        $effects->MindPlus = $request->get("MindPlus");
        $effects->PerceptionPlus = $request->get("PerceptionPlus");
        $effects->MinInstFireDmg = $request->get("MinInstFireDmg");
        $effects->MaxInstFireDmg = $request->get("MaxInstFireDmg");
        $effects->MinInstColdDmg = $request->get("MinInstColdDmg");
        $effects->MaxInstColdDmg = $request->get("MaxInstColdDmg");
        $effects->MinInstPoisonDmg = $request->get("MinInstPoisonDmg");
        $effects->MaxInstPoisonDmg = $request->get("MaxInstPoisonDmg");
        $effects->MinInstLightningDmg = $request->get("MinInstLightningDmg");
        $effects->MaxInstLightningDmg = $request->get("MaxInstLightningDmg");
        $effects->MinInstPhysicalDmg = $request->get("MinInstPhysicalDmg");
        $effects->MaxInstPhysicalDmg = $request->get("MaxInstPhysicalDmg");
        $effects->MinInstChiDmg = $request->get("MinInstChiDmg");
        $effects->MaxInstChiDmg = $request->get("MaxInstChiDmg");
        $effects->PowerRankPlusOne = $request->get("PowerRankPlusOne");
        $effects->Icon = $request->get("Icon");
        $effects->WeaponSpeed = $request->get("WeaponSpeed");
        $effects->Entangle = $request->get("Entangle");
        $effects->HardenDefense = $request->get("HardenDefense");
        $effects->Penetrate = $request->get("Penetrate");
        $effects->ConstHPPlus = $request->get("ConstHPPlus");
        $effects->ConstChiPlus = $request->get("ConstChiPlus");
        $effects->Strength = $request->get("Strength");
        $effects->Weakness = $request->get("Weakness");
        $effects->Icon2 = $request->get("Icon2");
        $effects->MinUnarmedDmgPlus = $request->get("MinUnarmedDmgPlus");
        $effects->MaxUnarmedDmgPlus = $request->get("MaxUnarmedDmgPlus");
        $effects->CriticalChance = $request->get("CriticalChance");
        $effects->CriticalHit = $request->get("CriticalHit");
        $effects->ThornDrain = $request->get("ThornDrain");
        $effects->ThornChi = $request->get("ThornChi");
        $effects->Blind = $request->get("Blind");
        $effects->Confusion = $request->get("Confusion");
        $effects->Bezerk = $request->get("Bezerk");
        $effects->Knockdown = $request->get("Knockdown");
        $effects->NoDispel = $request->get("NoDispel");
        $effects->MaxInstDirectDmg = $request->get("MaxInstDirectDmg");
        $effects->MinInstDirectDmg = $request->get("MinInstDirectDmg");
        $effects->ConstDirectDmg = $request->get("ConstDirectDmg");
        $effects->MinDirectDmgPlus = $request->get("MinDirectDmgPlus");
        $effects->MaxDirectDmgPlus = $request->get("MaxDirectDmgPlus");
        
        $effects->save();

        return Redirect::back()->with('success', 'Effect Updated!');
    }
    

    public function motd($id){
         $title = "MOTD";

         $motd = DB::connection("mysql3")->table('MOTD')
                ->where(['MOTD.id' => $id])
                ->first();


         return view('Admin/Tool/motd',compact('title','motd'));
    }

    public function storeMOTD(Request $request){

        DB::connection("mysql3")->table('MOTD')->where('id','=',$request->id)
        ->update(['MsgText1' => $request->MsgText1,
            'MsgText1' => $request->MsgText1,
            'MsgText2' => $request->MsgText2,
            'MsgText3' => $request->MsgText3,
            'MsgText4' => $request->MsgText4,
            'MsgText5' => $request->MsgText5,
            'MsgText6' => $request->MsgText6]);

        return Redirect::back()->with('success', 'MOTD Updated!');
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

     
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Support a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tres =  Treasure::where('Indx', '=', $id)->firstOrFail();

        $tres->delete();

        return Redirect::back()->with('unsuccess', 'Data Have Been Delete!');
    }


    public function upload(Request $request){
        dd($request);
        }
}

