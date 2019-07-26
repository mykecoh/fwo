<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Item;
use App\ItemCategory;
use App\Boost;
use App\User;
use DB;
use App\ChangeNickHistory;
use App\UnstuckLog;
use App\RewardLog;
use App\BoostAccount;
use App\FtLog;
//use App\Purchase;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unstuck()
    {   
        $title = "unstuck";

        $usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Support.unstuck',compact('title','usernames'));
    }

    public function unstuckSubmit(Request $request)
    {   

        $unstuck = DB::connection("mysql")->table("unstuck_log")->where('charid','=',$request->char_id)->orderBy('id', 'desc')->first();

        if($unstuck == "NULL" || empty($unstuck)){
            $sceneid = 150;
            $x = 4526.81445;
            $y = 4200.00000;
            $z = "-30678.80859";
            $BindSceneID = 150;
            $BindX = 6405.86670;
            $BindY = 4206.79053;
            $BindZ = "-28914.40820";

            $unstuckLogs = new UnstuckLog(); // save log
            
            $unstuckLogs->charid = $request->char_id;
            $unstuckLogs->to_x = $x;
            $unstuckLogs->to_y = $y;
            $unstuckLogs->to_z = $z;
            $unstuckLogs->to_scene = $sceneid;
            $unstuckLogs->date = date("Y-m-d");

            $unstuckLogs->save();

            DB::connection("mysql3")->table('pcharacter')->where('CharID','=',$request->char_id)->update(['sceneid' => $sceneid, 'x' => $x, 'y' => $y, 'z' => $z,'BindSceneID' => $BindSceneID, 'BindX' => $BindX, 'BindY' => $BindY, 'BindZ' => $BindZ]); 
            //DB::connection("mysql3")->table('pcharacter')->where('CharID','=',$character)->update([]); 
            return redirect('Support/unstuck')->with('success', 'Unstuck success!');
        }
        
        if($unstuck->date == date("Y-m-d")){
            return redirect('Support/unstuck')->with('unsuccess', 'Your Char Already Submit Today!');
        }
    }


    public function my_boost()
    {   
        $title = "My Boost Exp";
        
        $Runningboosts = DB::connection("mysql")->table('boost_account')
                    //->select('boost_account.*','pcharacter.CharacterName','boost.description','boost.exp','boost.credit','boost.discount','boost.time','boost.name')
                    //->join('fwworlddevdb.pcharacter','pcharacter.CharID','=','boost_account.charid')
                    ->join('boost','boost.id','=','boost_account.boostid')
                    ->where(['boost_account.uid' => auth()->user()->id])
                    ->where(['boost_account.status' => '2'])
                    ->get();

        $boosts = DB::connection("mysql")->table('boost_account')
                    ->select('boost_account.*','pcharacter.CharacterName','boost.description','boost.exp','boost.credit','boost.discount','boost.time','boost.name')
                    ->join('fwworlddevdb.pcharacter','pcharacter.CharID','=','boost_account.charid')
                    ->join('boost','boost.id','=','boost_account.boostid')
                    ->where(['boost_account.uid' => auth()->user()->id])
                    ->where('boost_account.status','!=','2')
                    ->orderBy('boost_account.status','Asc')
                    ->get();

        //$boosts = BoostAccount::find($id);

        return view('Support.my_boost',compact('title','boosts','Runningboosts'));
    }

    public function useBoost(Request $request, $id)
    {   
        $boost = BoostAccount::find($id);

        if (DB::connection("mysql")->table('boost_account')->where('CharID', '=', $boost->charid)->where('status', '=',2)->count() >= 1) {
           // user found
            return redirect('Support/my_boost')->with('unsuccess', "You Already Use The Boost!.");
        }
        
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $dateStart = date('Y-m-d H:i:s'); //Returns IST

        $dateEnd = (date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +".$boost->time." minutes")));

        DB::connection("mysql")->table('boost_account')->where('id','=',$boost->id)->update(['timestart' => $dateStart, 'timeexpired' => $dateEnd, 'status' => '2']);
        DB::connection("mysql3")->table('pcharstats_all')->where('CharID','=',$boost->charid)->update(['MulPerc' => $boost->boostxp]);
        return redirect('Support/my_boost')->with('success', "Boost Start!.");
    }

    public function forge()
    {   
        $title = "forge";

        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

        $pcharacter = DB::connection("mysql3")->table('pcharacter')
                        ->where('pcharacter.Username','=',auth()->user()->username)
                        ->get();

        $categories = DB::connection("mysql")->table('item_category')
                        ->where('id','!=','33')->where('id','!=','17')->where('id','!=','16')->where('id','!=','7')->where('id','!=','11')->where('id','!=','9')
                        ->get();



        for($i = 0; $i < count($pcharacter); $i++){
            $Char[$i] = DB::connection("mysql3")->table('charinv_all')
                    ->select('charinv_all.*','db2.value','pcharacter.CharacterName')
                    //->select('charinv_all.*','item.LevelGroup','string.*','pcharacter.*')
                    //->join('item','item.ItemID','=','charinv_all.ItemID')
                    //->join('string','string.id','=','charinv_all.ItemID')
                    ->join('DB.string as db2','charinv_all.ItemID','=','db2.id')
                    ->join('pcharacter','pcharacter.CharID','=','charinv_all.CharID')
                    ->whereBetween('SlotNum', array(151, 156))
                    ->whereIn('charinv_all.CharID',[$pcharacter[$i]->CharID])
                    //->where('charinv_all.ItemID','!=','0')
                    //->where('item.LevelGroup','>=','41')
                    //->where('item.NoTransFlag','=','0')
                    //->where('item.ItemID','<=','1370000')
                    ->groupBy('SlotNum')
                    ->orderBy('charinv_all.SlotNum')
                    ->get();
        }

        // dd($Char[1]);
                    // 1073894216

        $markets = DB::connection("mysql")->table('market_item')
                        ->select('market_item.*','string.value','item_category.name')
                        ->join('string','string.id','=','market_item.itemid')
                        ->join('item_category','item_category.id','=','market_item.item_category')
                        ->where('market_item.seller','=',auth()->user()->username)
                        ->orderBy('id','DESC')
                        ->get();


         
        return view('Support.forge',compact('purchase','title','Char','categories','markets','users'));
    }

    public function nickname()
    {   
         $title = "nickname";

        $usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();
        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();


         
         return view('Support.nickname',compact('title','usernames','users'));
    }

    public function changeNickname(Request $request){



        if($request->credit <= 40000){
            return redirect('Support/nickname')->with('unsuccess', "Not enough credit!.");
        }
        
        if(empty($request->charname) || $request->charname == "NULL"){
            return redirect('Support/nickname')->with('unsuccess', "Please Select Your Character Name!.");
        }

        if(empty($request->change_charname) || $request->change_charname == "NULL"){
            return redirect('Support/nickname')->with('unsuccess', "Please Fill Your Character Name!.");
        }

        if(strlen($request->change_charname) < 2) {
            return redirect('Support/nickname')->with('unsuccess', 'Invalid new nickname!');
        }
        
        if(strlen($request->change_charname) > 20) {
            return redirect('Support/nickname')->with('unsuccess', 'Invalid new nickname!');
        }
        
        
        if(!mb_check_encoding($request->change_charname, 'UTF-8')) {
            return redirect('Support/nickname')->with('unsuccess', 'Invalid new nickname!');
        }
        
        if (preg_match('/[\'^:"?!@#$%^&*()£$%&*()}{@#~?><>,|=_+¬-]/', $request->change_charname))
        {
            return redirect('Support/nickname')->with('unsuccess','Invalid new nickname!');
        }
        
        if (preg_match("/\\s/", $request->change_charname)) {
            return redirect('Support/nickname')->with('unsuccess', 'Invalid new nickname!');
        }
        
        $disable_array = array("allah","nabi","babi","gm","gamemaster","gm","admin");
        if(in_array(strtolower($request->change_charname),$disable_array)) {
            return redirect('Support/nickname')->with('unsuccess', 'Nickname cannot contain offensive, racist, religion related and swear words!');
        }

        $charID = DB::connection("mysql3")->table('pcharacter')->where('CharID','=',$request->charname)->first();

        $changenichis = new ChangeNickHistory(); // save log
        
        $changenichis->charid = $request->charname;
        $changenichis->oldnickname = $charID->CharacterName;
        $changenichis->newnick = $request->change_charname;
        $changenichis->date = date("Y-m-d");

        $changenichis->save();
        

        $old_nick = $charID->CharacterName;
        $character = $request->charname;
        $newnick = $request->change_charname;

        

         $GLOBALS['__crc32_table']=array();        // Lookup table array
         $this->__crc32_init_table();
        

        $charactername_u16 = $this->U8toU16(trim(stripslashes($newnick)));
        $charactername_u16 = substr_replace(str_repeat("\0", 38), $charactername_u16, 0, strlen($charactername_u16));
        $hashnick = sprintf("%u", $this->__crc32_string($charactername_u16));
        $charnm = '0x'. $this->hexstring($charactername_u16) . '0000';

        // var_dump($charnm);
        // die();

        $credit = $request->credit - 40000;
        DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->update(['credit' => $credit]);    // tolak balance credit 




        //$this->changenick($character,$newnick,$hashnick);

        DB::connection("mysql3")->table('pcharacter')->where('CharID','=',$character)->update(['CharacterName' => $charactername_u16,'HashValue' => $hashnick]); 
        //DB::connection("mysql3")->table('pcharacter')->where('CharID','=',$character)->update([]); 
        return redirect('Support/nickname')->with('success', 'Nickname Changed!');
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

    public function claim()
    {   
         $title = "Claim Reward";


        $charInfos = DB::connection("mysql3")->table('pcharacter')
        ->where(['pcharacter.Username' => auth()->user()->username])
        ->join('pcharstats_all','pcharstats_all.CharID','=','pcharacter.CharID')
        ->get();

        if(!empty($charInfos[0])){
            $rewardLogs = DB::table('reward_log')
            ->where('reward_log.charid', '=', $charInfos[0]->CharID)
            ->orderBy('reward_id', 'ASC')
            ->get();
        }else{
            
        }

        if(!empty($charInfos[1])){
            $rewardLogs1 = DB::table('reward_log')
            ->where('reward_log.charid', '=', $charInfos[1]->CharID)
            ->get();
        }else{

        }

        if(!empty($charInfos[2])){
            $rewardLogs2 = DB::table('reward_log')
            ->where('reward_log.charid', '=', $charInfos[2]->CharID)
            ->get();
        }else{

        }
            
        

         $rewards = DB::table('reward_capped')->orderBy('id', 'ASC')->get();
       


         return view('Support.claim',compact('title','charInfos','rewards','rewardLogs','rewardLogs1','rewardLogs2'));
    }

    public function bind()
    {   
         $title = "bind";

         
         return view('Support.bind',compact('title'));
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
        //
        $rewardLog = new RewardLog();

        $rewardLog->reward_id = $request['reward_id'];
        $rewardLog->charid = $request['charid'];
        $rewardLog->date = date("Y-m-d");

        $rewardLog->save();

        return redirect('/Support/claim')->with('success','Add successfully');

    }

    public function storeForge(Request $request)
    {
        

        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
        //dd($users->credit);
        if (!User::where('username', request('username'))->first()){
            return redirect('Support/forge')->with('unsuccess', "User does not exists!.");
        }elseif($users->ft <= request('ft')){
            return redirect('Support/forge')->with('unsuccess', "Not enough FT!.");
        }elseif($request->username == auth()->user()->username){
            return redirect('Support/forge')->with('unsuccess', "Cannot transfer to self!.");
        }else{
            $FT_log = new FtLog();
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $FT_log->from_user = auth()->user()->username;
            $FT_log->to_user = request('username');
            $FT_log->amount = request('ft');
            $FT_log->date = date("d-m-Y h:i A");
            $FT_log->save();

            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->decrement('ft',request('ft'));
            DB::connection("mysql")->table('users')->where('username','=',request('username'))->increment('ft',request('ft'));
            return redirect('Support/forge')->with('success', "Your FT Has Been Transferred.");
        }

    }

    public function claimStore(Request $request)
    {
        //
        $RewardID = DB::connection("mysql")->table('reward_capped')->where('id','=',$request->rewardID)->first();
        $FindItem = DB::connection("mysql3")->table('item')->where('ItemID','=',$RewardID->item)->first();

        //dd($RewardID->ft);

        $SlotCount = DB::connection("mysql3")->table('charinv_all')
                    ->where('CharID','=',$request->CharId)
                    ->where('itemID','=','0')
                    ->whereBetween('SlotNum', array(86, 133))
                    ->count();

        $id_slots = DB::connection("mysql3")->table('charinv_all')
                                ->where('CharID','=',$request->CharId)
                                ->where('itemID','=','0')
                                ->whereBetween('SlotNum', array(86, 133))
                                ->take(1)
                                ->orderBy('SlotNum', 'asc')->first();

        // dd($id_slots);

        if (DB::connection("mysql3")->table('authenticated')->where('Username', '=', auth()->user()->username)->count() > 0) {
           // user found
            return redirect('Market/sell_item')->with('unsuccess', "Please Log Out First!.");
        }

        if($SlotCount < 1) {
            return redirect('/Market/sell_item')->with('unsuccess','Not enough free slot in character inventory!');
        }

        if($RewardID->ft != 0){
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->increment('ft',$RewardID->ft);
        }elseif($RewardID->bt != 0){
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->increment('bt',$RewardID->bt);
        }else{
            DB::connection("mysql3")->table('charinv_all')
            ->where('SlotNum','=',$id_slots->SlotNum)
            ->where('Indx','=',$id_slots->Indx)
            ->update(['ItemID' => $FindItem->ItemID,'Quantity' => $RewardID->item_quantity, 'Identified' => '$FindItem->Identify', 'Durability' => "100", 
                    'Field1' => $FindItem->Field1, 'Field2' => $FindItem->Field2, 'Field3' => $FindItem->Field3, 'Field4' => $FindItem->Field4, 
                    'Field5' => $FindItem->Field5, 'Hardness' => $FindItem->Hardness, 'Level' => $FindItem->LevelGroup]);
        }

        $rewardLog = new RewardLog();
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $rewardLog->reward_id = $RewardID->id;
        $rewardLog->charid = $request->CharId;
        $rewardLog->username = auth()->user()->username;
        $rewardLog->status = '1';
        $rewardLog->date = date("d-m-Y g:i A");

        $rewardLog->save();
        return redirect()->back()->with('success', 'Claim Success!');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
