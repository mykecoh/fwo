<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ItemCategory;
use App\Item;
use App\DevWorld;
use DB;
use App\MarketCharacter;
use App\MarketFt;
use App\MarketBt;
use App\MarketItem;
use Illuminate\Support\Facades\Input;
//use App\Purchase;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function market($id)
    {   
        $title = "Market";
        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

        $menuItems = ItemCategory::where('id','!=','33')->where('id','!=','17')->where('id','!=','16')->get();
        

        $markets = DB::connection("mysql")->table('market_item')
                    ->select('item_category.*','market_item.*','string.value')
                    ->join('item_category','item_category.id','=','market_item.item_category')
                    ->join('string','string.id','=','market_item.itemid')
                    ->where(['market_item.item_category' => $id])
                    ->where(['market_item.status' => '1'])
                    ->get();

        $chars = DB::connection("mysql")->table('market_character')
                    ->join('fwworlddevdb.pcharacter as db2','market_character.charid','=','db2.CharID')
                    ->join('fwworlddevdb.pcharstats as db3','market_character.charid','=','db3.CharID')
                    ->where(['market_character.status' => '1','market_character.type' => '2'])
                    ->get();

        $stance = DB::connection("mysql3")->table('stancelist_all')
                    ->join('DB.market_character as db2','stancelist_all.CharID','=','db2.charid')
                    ->join('DB.stance as db3','stancelist_all.StanceID','=','db3.id')
                    ->where('stancelist_all.StanceID','!=','0')
                    ->where('status','=','1')
                    ->orderBy('stancelist_all.StanceID')
                    ->get();

        $pcharacter = DB::connection("mysql3")->table('pcharacter')
                        ->where('pcharacter.Username','=',auth()->user()->username)
                        ->get();

        $marketFts = DB::connection("mysql")->table('market_ft')
                    ->join('users','users.id','=','market_ft.uid')
                    ->select('users.username','market_ft.*')
                    ->where('market_ft.status','=','0')->get();

        $marketBts = DB::connection("mysql")->table('market_bt')
                    ->join('users','users.id','=','market_bt.uid')
                    ->select('users.username','market_bt.*')
                    ->where('market_bt.status','=','0')->get();

        $Weaponmarkets = DB::connection("mysql")->table('market_item')
                    ->select('item_category.*','market_item.*','string.value')
                    ->join('item_category','item_category.id','=','market_item.item_category')
                    ->join('string','string.id','=','market_item.itemid')
                    ->where(['market_item.item_category' => $id])
                    ->where(['market_item.status' => '1'])
                    ->get();

        $WeaponSlot1 = DB::connection("mysql")->table('market_item')
                    ->select('market_item.*','string.*')
                    ->join('string','string.id','=','market_item.Field1')
                    ->where(['market_item.item_category' => $id])
                    ->first(); 

        $WeaponSlot2 = DB::connection("mysql")->table('market_item')
                    ->select('market_item.*','string.*')
                    ->join('string','string.id','=','market_item.Field2')
                    ->where(['market_item.item_category' => $id])
                    ->first(); 

        $WeaponSlot3 = DB::connection("mysql")->table('market_item')
                    ->select('market_item.*','string.*')
                    ->join('string','string.id','=','market_item.Field3')
                    ->where(['market_item.item_category' => $id])
                    ->first(); 

        $WeaponSlot4 = DB::connection("mysql")->table('market_item')
                    ->select('market_item.*','string.*')
                    ->join('string','string.id','=','market_item.Field4')
                    ->where(['market_item.item_category' => $id])
                    ->first(); 

        $WeaponSlot5 = DB::connection("mysql")->table('market_item')
                    ->select('market_item.*','string.*')
                    ->join('string','string.id','=','market_item.Field5')
                    ->where(['market_item.item_category' => $id])
                    ->first(); 

        // dd($WeaponSlot1);
        
        $DetailChars = DB::connection("mysql3")->table('pcharacter')
                    ->join('DB.users','users.username','=','pcharacter.Username')
                    ->where('users.id','=',auth()->user()->id)
                    ->get();

        $idtab = $id;

        return view('Market.market',compact('title','markets','itemCategories','menuItems','idtab','chars','stance','pcharacter','marketFts',
                                    'users','marketBts','Weaponmarkets','WeaponSlot1','WeaponSlot2','WeaponSlot3','WeaponSlot4','WeaponSlot5','DetailChars'));
    }

    public function sell_item()
    {   
         $title = "Sell Item";
         //$purchase = Purchase::all();

        $pcharacter = DB::connection("mysql3")->table('pcharacter')
                        ->where('pcharacter.Username','=',auth()->user()->username)
                        ->get();

        $categories = DB::connection("mysql")->table('item_category')
                        ->where('id','!=','33')->where('id','!=','17')->where('id','!=','16')->where('id','!=','7')->where('id','!=','11')->where('id','!=','9')
                        ->get();



        for($i = 0; $i < count($pcharacter); $i++){
            $Char[$i] = DB::connection("mysql3")->table('charinv_all')
                    ->select('charinv_all.*','item.LevelGroup','db2.*','pcharacter.*')
                    ->join('item','item.ItemID','=','charinv_all.ItemID')
                    //->join('string','string.id','=','charinv_all.ItemID')
                    ->join('DB.string as db2','charinv_all.ItemID','=','db2.id')
                    ->join('pcharacter','pcharacter.CharID','=','charinv_all.CharID')
                    ->whereBetween('SlotNum', array(151, 156))
                    ->whereIn('charinv_all.CharID',[$pcharacter[$i]->CharID])
                    ->where('charinv_all.ItemID','!=','0')
                    ->where('item.LevelGroup','>=','41')
                    ->where('item.NoTransFlag','=','0')
                    ->where('item.ItemID','<=','1370000')
                    ->get();
        }
                    // 1073894216

        $markets = DB::connection("mysql")->table('market_item')
                        ->select('market_item.*','string.value','item_category.name')
                        ->join('string','string.id','=','market_item.itemid')
                        ->join('item_category','item_category.id','=','market_item.item_category')
                        ->where('market_item.seller','=',auth()->user()->username)
                        ->orderBy('id','DESC')
                        ->get();

        // dd($markets);

         return view('Market.sell_item',compact('purchase','title','Char','Char','categories','markets'));
    }

    public function editSellItem($id){
        $title = "Sell Character";

        $itemCategories = DB::connection("mysql")->table("item_category")->where('item_mall','=','1')->where('id','!=','33')->where('id','!=','17')->where('id','!=','16')->get();

        $MarketItem = DB::connection("mysql")->table('market_item')
                        ->select('market_item.*','string.value')
                        ->join('string','string.id','=','market_item.itemid')
                        ->where('market_item.id','=',$id)
                        ->first();

        return view('Market.editSellItem',compact('title','MarketItem','itemCategories'));
    }

    public function updateSellItem(Request $request, $id){
            
            $marketItem = MarketItem::find($id);

            $marketItem->item_category =  $request->item_category_id;
            $marketItem->credit = $request->credit;

            $marketItem->save();

            return redirect('Market/sell_item')->with('success', 'Sell Item updated!');  
    }

    public function sell_character(Request $request)
    {   
         $title = "Sell Character";

         
         if(request('submit') == "cancel"){
            $releasesell = strtotime('+1 day', request('timesell'));

            //dd($releasesell);
            // if(empty(request('charID'))){
            //     return redirect('/Market/sell_character')->with('unsuccess','Please Select Your Character!');
            // }

            if(strtotime("now") <= $releasesell){
                return redirect('/Market/sell_character')->with('unsuccess','Character offer cancel need to wait for 1 day!');
            }
            
            date_default_timezone_set("Asia/Kuala_Lumpur");
            DB::connection("mysql")->table('market_character')->where('charid','=',request('charid'))->update(['status' => '2','timebuy' => strtotime("now")]);
            return redirect('/Market/sell_character')->with('success','Character offer has been cancel!');
            
         }
         //$purchase = Purchase::all();

        $pcharacter = DB::connection("mysql3")->table('pcharacter')
                        ->where('pcharacter.Username','=',auth()->user()->username)
                        ->get();

        $charsMarkets = DB::connection("mysql")->table('market_character')
                    ->join('fwworlddevdb.pcharacter as db2','market_character.charid','=','db2.CharID')
                    ->join('fwworlddevdb.pcharstats as db3','market_character.charid','=','db3.CharID')
                    ->where(['market_character.seller' => auth()->user()->username])
                    ->orWhere(['market_character.buyer' => auth()->user()->username])
                    ->orderBy('market_character.id','DESC')
                    ->get();

        // dd($charsMarkets);


        for($a = 0; $a < count($charsMarkets); $a++){
            if($charsMarkets[$a]->expired <= strtotime("now") && $charsMarkets[$a]->status == '1'){
                DB::connection("mysql")->table('market_character')->where('charid','=',$charsMarkets[$a]->charid)->where('status','=','1')->update(['status' => '0']);
            }
        }
        //dd(strtotime("now"));



         return view('Market.sell_character',compact('purchase','title','pcharacter','charsMarkets'));
    }

    public function buy()
    {   
        $title = "Buy Character";
         //$purchase = Purchase::all();
        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

        $chars = DB::connection("mysql")->table('market_character')
                    ->join('fwworlddevdb.pcharacter as db2','market_character.charid','=','db2.CharID')
                    ->join('fwworlddevdb.pcharstats as db3','market_character.charid','=','db3.CharID')
                    ->where(['market_character.buyer' => auth()->user()->username,'market_character.status' => '1'])
                    ->get();

        $stance = DB::connection("mysql3")->table('stancelist_all')
                    ->join('DB.market_character as db2','stancelist_all.CharID','=','db2.charid')
                    ->join('DB.stance as db3','stancelist_all.StanceID','=','db3.id')
                    ->where('stancelist_all.StanceID','!=','0')
                    ->where('status','=','1')
                    ->orderBy('stancelist_all.StanceID')
                    ->get();

        // dd($stance);


         return view('Market.buy',compact('chars','title','stance','users'));
    }

    public function sell_forge()
    {   
         $title = "Sell Forge Ticket";
         //$purchase = Purchase::all();
         $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

         $marketFts = DB::connection("mysql")->table('market_ft')->where('uid','=',auth()->user()->id)->where('status','!=','3')->orderBy('id','DESC')->get();

         return view('Market.sell_forge',compact('users','title','marketFts'));
    }

    public function sell_bind()
    {   
         $title = "Sell Bind Ticket";
         //$purchase = Purchase::all();
         $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

         $marketBts = DB::connection("mysql")->table('market_bt')->where('uid','=',auth()->user()->id)->where('status','!=','3')->orderBy('id','DESC')->get();

         return view('Market.sell_bind',compact('users','title','marketBts'));
    }

    public function storeForge()
    {
        $quantity = request('quantity');
        $credit = request('credit');

        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
        $ft = $users->ft;
        
        $quantity_brg = DB::connection("mysql")->table('market_ft')->where('uid','=',auth()->user()->id)->where('status','=','0')->sum('quantity');
        $kuantitisemua = $quantity_brg+$quantity;

        if(request('credit') == "") {
            return redirect('/Market/sell_forge')->with('unsuccess','Please Insert Value Credit!');
        }

        if($quantity > $ft) {
            return redirect('/Market/sell_forge')->with('unsuccess','Not Enough Forge Ticket!');
        }
        
        if($quantity <= 0) {
            return redirect('/Market/sell_forge')->with('unsuccess','Forge Ticket Error!');
        }
        
        if($kuantitisemua > 6) {
            return redirect('/Market/sell_forge')->with('unsuccess','Only 6 Forge Ticket allowed to sell at 1 time!');
        }

        // dd($quantity_brg);
        
        if($credit <= 0) {
            return redirect('/Market/sell_forge')->with('unsuccess','Credit Error!');
        }

        // if (MarketFt::where('uid', '=', auth()->user()->id)->where('status', '=', '0')->exists()) {
        //     return redirect('/Market/sell_forge')->with('unsuccess','Your Forge Ticket Already on Sale');
        // }

        $marketFt = new MarketFt();
        
        $marketFt->uid = auth()->user()->id;
        $marketFt->quantity = request('quantity');
        $marketFt->credit = request('credit');
        $marketFt->date_sell = strtotime("now");
        $marketFt->status = 0;

        $marketFt->save();
        return redirect('/Market/sell_forge')->with('success','Success put Forge Ticket in Market Place!');
    }

    public function storeBind()
    {
        $quantity = request('quantity');
        $credit = request('credit');

        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
        $bt = $users->bt;
        
        $quantity_brg = DB::connection("mysql")->table('market_bt')->where('uid','=',auth()->user()->id)->where('status','=','0')->sum('quantity');
        $kuantitisemua = $quantity_brg+$quantity;

        if(request('credit') == "") {
            return redirect('/Market/sell_bind')->with('unsuccess','Please Insert Value Credit!');
        }

        if($quantity > $bt) {
            return redirect('/Market/sell_bind')->with('unsuccess','Not Enough Forge Ticket!');
        }
        
        if($quantity <= 0) {
            return redirect('/Market/sell_bind')->with('unsuccess','Forge Ticket Error!');
        }
        
        if($kuantitisemua > 6) {
            return redirect('/Market/sell_bind')->with('unsuccess','Only 6 Forge Ticket allowed to sell at 1 time!');
        }

        // dd($quantity_brg);
        
        if($credit <= 0) {
            return redirect('/Market/sell_bind')->with('unsuccess','Credit Error!');
        }
        
        $marketFt = new MarketBt();
        
        $marketFt->uid = auth()->user()->id;
        $marketFt->quantity = request('quantity');
        $marketFt->credit = request('credit');
        $marketFt->date_sell = strtotime("now");
        $marketFt->status = 0;

        $marketFt->save();
        return redirect('/Market/sell_bind')->with('success','Success put Forge Ticket in Market Place!');
    }

    public function buyFT(Request $request)
        {    
            $FT = MarketFT::where('id', '=', $request->id)->firstOrFail();

            $Sellers = DB::connection("mysql")->table('users')->where('id','=',$FT->uid)->first();
            $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
            

            if($FT->user_id == auth()->user()->id) {
                return redirect('/Market/market/32')->with('unsuccess','Cannot buy own Forge Ticket!');
            }
            
            if($FT->quantity == 0) {
                return redirect('/Market/market/32')->with('unsuccess','Forge Ticket is not available! ');
            }

            if($FT->quantity == "") {
                return redirect('/Market/market/32')->with('unsuccess','Please Insert Value Credit! ');
            }
            
            if($users->credit < $FT->credit) {
                return redirect('/Market/market/32')->with('unsuccess','Not enough credit! ');
            }

            DB::connection("mysql")->table('users')->where('id','=',$FT->uid)->decrement('credit',$FT->credit);
            DB::connection("mysql")->table('users')->where('id','=',$FT->uid)->decrement('ft',$FT->quantity);
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->increment('credit',$FT->credit);
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->increment('ft',$FT->quantity);
            DB::connection("mysql")->table('market_ft')->where('id','=',$FT->id)->update(['status' => '1','date_buy' => strtotime("now"),'to_id' => auth()->user()->id]);
            return redirect('/Market/market/32')->with('success','Purchase Forge Ticket success!');
            
        }

    public function buyBT(Request $request)
        {      
            
            $BT = MarketBT::where('id', '=', $request->id)->firstOrFail();

            $Sellers = DB::connection("mysql")->table('users')->where('id','=',$BT->uid)->first();
            $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
            

            if($BT->user_id == auth()->user()->id) {
                return redirect('/Market/market/32')->with('unsuccess','Cannot buy own Forge Ticket!');
            }
            
            if($BT->quantity == 0) {
                return redirect('/Market/market/32')->with('unsuccess','Forge Ticket is not available! ');
            }

            if($BT->quantity == "") {
                return redirect('/Market/market/32')->with('unsuccess','Please Insert Value Credit! ');
            }
            
            if($users->credit < $BT->credit) {
                return redirect('/Market/market/32')->with('unsuccess','Not enough credit! ');
            }

            DB::connection("mysql")->table('users')->where('id','=',$BT->uid)->decrement('credit',$BT->credit);
            DB::connection("mysql")->table('users')->where('id','=',$BT->uid)->decrement('ft',$BT->quantity);
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->increment('credit',$BT->credit);
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->increment('ft',$BT->quantity);
            DB::connection("mysql")->table('market_ft')->where('id','=',$BT->id)->update(['status' => '1','date_buy' => strtotime("now"),'to_id' => auth()->user()->id]);
            return redirect('/Market/market/33')->with('success','Purchase Forge Ticket success!');
            
        }


    public function cancelFT(Request $request)
    {   
         DB::connection("mysql")->table('market_ft')->where('id','=',request('id'))->update(['status' => '3']); 
         return redirect('/Market/sell_forge')->with('unsuccess','Success Cancel!');
    }

    public function cancelBT(Request $request)
    {   
         DB::connection("mysql")->table('market_bt')->where('id','=',request('id'))->update(['status' => '3']); 
         return redirect('/Market/sell_bind')->with('unsuccess','Success Cancel!');
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $Item = MarketItem::where('id', '=', $request->id)->firstOrFail();


        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

        $SlotCount = DB::connection("mysql3")->table('charinv_all')
                    ->where('CharID','=',$request->charID)
                    ->where('itemID','=','0')
                    ->whereBetween('SlotNum', array(86, 133))
                    ->count();

        $id_slots = DB::connection("mysql3")->table('charinv_all')
                                ->where('CharID','=',$request->charID)
                                ->where('itemID','=','0')
                                ->whereBetween('SlotNum', array(86, 133))
                                ->take(1)
                                ->orderBy('SlotNum', 'asc')->first();
        
        if($Item->status != 1) {
            return redirect()->back()->with('unsuccess','Sold Out!');
        }

        if($Item->seller == auth()->user()->username) {
            return redirect()->back()->with('unsuccess','Cannot Buy Own Item!');
        }
        
        if($Item->credit > $users->credit) {
            return redirect()->back()->with('unsuccess','Not Enough Credit!');
        }

        if (DB::connection("mysql3")->table('authenticated')->where('Username', '=', auth()->user()->username)->count() > 0) {
           // user found
            return redirect()->back()->with('unsuccess', "Please Log Out First!.");
        }
        
        if($SlotCount < 1) {
            return redirect()->back()->with('unsuccess','Not enough free slot in character inventory!');
        }


        DB::connection("mysql3")->table('charinv_all')
        ->where('SlotNum','=',$id_slots->SlotNum)
        ->where('Indx','=',$id_slots->Indx)
        ->update(['ItemID' => $Item->itemid,'Quantity' => $Item->quantity, 'Identified' => $Item->identified, 'Durability' => $Item->durability, 
                    'Field1' => $Item->field1, 'Field2' => $Item->field2, 'Field3' => $Item->field3, 'Field4' => $Item->field4, 
                    'Field5' => $Item->field5, 'Hardness' => $Item->hardness, 'Level' => $Item->level]);
        DB::connection("mysql")->table('market_item')->where('id','=',$request->id)->update(['status' => '3','buyer' => auth()->user()->username]);

        DB::connection("mysql")->table('users')->where('username','=',$Item->seller)->increment('credit',$Item->credit);
        DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->decrement('credit',$Item->credit);

        return redirect()->back()->with('success', 'Buy Success!');

    }

     public function storeSellItem(Request $request)
    {
        $countItem = DB::connection("mysql")->table('market_item')
                    ->where('charid', '=', request('charid'))
                    ->where('seller', '=', auth()->user()->username)->count();

        

        if (DB::connection("mysql3")->table('authenticated')->where('CharID', '=', request('charid'))->count() > 0) {
           // user found
            return redirect()->back()->with('unsuccess', "Please Log Out First!.");
        }

        if(empty(request('credit'))){
            return redirect()->back()->with('unsuccess', "Please Insert Credit!.");  
        }

        if(empty(request('item_category'))){
            return redirect()->back()->with('unsuccess', "Please Insert Category!.");  
        }

        if($countItem >= 6){
            return redirect()->back()->with('unsuccess', "You Already Sell 6 Item!.");  
        }
        // dd($countItem);
        // die();
        // if () {
        //     return redirect()->back()->with('unsuccess', "Please Log Out First!.");
        // }

        $marketItem = new MarketItem();

        $marketItem->uid = auth()->user()->id;
        $marketItem->charid = request('charid');
        $marketItem->seller = request('seller');
        $marketItem->itemid = request('itemid');
        $marketItem->item_category = request('item_category');
        $marketItem->level = request('level');
        $marketItem->quantity = request('quantity');
        $marketItem->identified = request('identified');
        $marketItem->durability = request('durability');
        $marketItem->hardness = request('hardness');
        $marketItem->field1 = request('field1');
        $marketItem->field2 = request('field2');
        $marketItem->field3 = request('field3');
        $marketItem->field4 = request('field4');
        $marketItem->field5 = request('field5');
        $marketItem->credit = request('credit');
        $marketItem->time = strtotime("now");
        $marketItem->expired = strtotime('+3 day',$marketItem->time);
        $marketItem->status = 1;

        $marketItem->save();
        //dd($request->Indx);
        //dd($request->charid);

        DB::connection("mysql3")->table('charinv_all')
        ->where('Indx','=',request('Indx'))
        ->update(['ItemID' => '0','Quantity' => '0','Identified' => '0','Durability' => '0','Field1' => '0','Field2' => '0','Field3' => '0','Field4' => '0','Field5' => '0','Hardness' => '0','Level' => '0']);

        return redirect('/Market/sell_item')->with('success','Success put Item in Market Place!');
    }

    public function storeCharacter(Request $request)
    {
        //
         $this->validate($request, [
            'type' => 'required',
        ]);

        if (MarketCharacter::where('charID', '=', Input::get('charID'))->where('status', '=', '1')->exists()) {
            return redirect('/Market/sell_character')->with('unsuccess','Your Character Already in Market');
        }

        if(request('type') == '1'){
            if (!DB::connection("mysql2")->table('subscription')->where('subscription.Username','=',request('buyer_username'))->exists()) {
                return redirect('/Market/sell_character')->with('unsuccess','Username does exists!');
            } 
        }

        if (MarketCharacter::where('buyer', '=', Input::get('buyer_username'))->where('status', '=', '1')->exists()) {
            return redirect('/Market/sell_character')->with('unsuccess','Character is already on sell offer!');
        }

        $pcharstats = DB::connection("mysql3")->table('pcharstats_all')
                        ->where('pcharstats_all.CharID','=',request('charID'))
                        ->first();

        if(auth()->user()->username == request('buyer_username')){
            return redirect('/Market/sell_character')->with('unsuccess','Cannot sell to own account!');
        }

        if($pcharstats->Level <= 50){
            return redirect('/Market/sell_character')->with('unsuccess','Your Character Must Be Level 50');
        }
        
        DB::connection("mysql3")->table('pcharacter')->where('CharID','=',request('charID'))->update(['Username' => ""]);

        $marketChar = new MarketCharacter();
    
        $marketChar->charid = request('charID');
        $marketChar->buyer = request('buyer_username');
        $marketChar->credit = request('credit');
        $marketChar->seller = request('seller');
        $marketChar->status = 1;
        $marketChar->ip_uid = request()->ip();
        $marketChar->type = request('type');
        $marketChar->timesell = strtotime("now");
        $marketChar->expired = strtotime('+3 day',$marketChar->timesell);


        $marketChar->save();

        if(request('type') == '1'){
            return redirect('/Market/sell_character')->with('success','Character sell offer success!');
        }else{
            return redirect('/Market/sell_character')->with('success','Sell chacracter on Open Market Success!');
        }
        

    }

    public function cancelItem(Request $request){
        
        $now = strtotime("now");
        $SlotCount = DB::connection("mysql3")->table('charinv_all')
                    ->where('CharID','=',$request->charid)
                    ->where('itemID','=','0')
                    ->whereBetween('SlotNum', array(86, 133))
                    ->count();

        $id_slots = DB::connection("mysql3")->table('charinv_all')
                                ->where('CharID','=',$request->charid)
                                ->where('itemID','=','0')
                                ->whereBetween('SlotNum', array(86, 133))
                                ->take(1)
                                ->orderBy('SlotNum', 'asc')->first();

        if(strtotime("+1 day",$request->time) > $now) {
            return redirect('/Market/sell_item')->with('unsuccess','Please wait 1 day to cancel sell item!');
        }

        if(auth()->user()->username != $request->seller) {
            return redirect('/Market/sell_item')->with('unsuccess','This is not your item!');
        }
        
        if($request->status != 1) {
            return redirect('/Market/sell_item')->with('unsuccess','Sold Out!');
        }
        
        if (DB::connection("mysql3")->table('authenticated')->where('Username', '=', auth()->user()->username)->count() > 0) {
           // user found
            return redirect('Market/sell_item')->with('unsuccess', "Please Log Out First!.");
        }

        
        if($SlotCount < 1) {
            return redirect('/Market/sell_item')->with('unsuccess','Not enough free slot in character inventory!');
        }

        //status = 2, cancel
        DB::connection("mysql3")->table('charinv_all')
        ->where('SlotNum','=',$id_slots->SlotNum)
        ->where('Indx','=',$id_slots->Indx)
        ->update(['ItemID' => $request->itemid,'Quantity' => $request->quantity, 'Identified' => $request->identified, 'Durability' => $request->durability, 
                    'Field1' => $request->field1, 'Field2' => $request->field2, 'Field3' => $request->field3, 'Field4' => $request->field4, 
                    'Field5' => $request->field5, 'Hardness' => $request->hardness, 'Level' => $request->level]);

        DB::connection("mysql")->table('market_item')->where('id','=',$request->id)->update(['status' => '2']);
        return redirect()->back()->with('success', 'Cancel Success!');
    }

    public function buyCharacter(Request $request){

                 $Char = MarketCharacter::where('id', '=', $request->id)->firstOrFail();
                 $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

                if (DB::connection("mysql3")->table('pcharacter')->where('Username', '=', auth()->user()->username)->count() >= 3) {
                    // user found
                    return redirect()->back()->with('unsuccess','No More Slot For Character!');
                }
                
                if($Char->credit > $users->credit){
                    return redirect()->back()->with('unsuccess','Not enough credit!');
                }  

                if($Char->seller == auth()->user()->username){
                    return redirect()->back()->with('unsuccess','Cannot Buy Your Own Sale!');
                }  

                if(!empty($Char->buyer)){
                    if($Char->buyer != auth()->user()->username){
                        return redirect()->back()->with('unsuccess', 'This transfer id is not yours!');
                    }    
                }
                
                if (DB::connection("mysql3")->table('authenticated')->where('CharID', '=', $Char->charid)->count() > 0) {
                    return redirect()->back()->with('unsuccess', "Buy is Online!.");
                }

                if (DB::connection("mysql3")->table('authenticated')->where('Username', '=', auth()->user()->username)->count() > 0) {
                    return redirect()->back()->with('unsuccess', "You Login is Login. Please Log Out First!.");
                }

            $Slot = DB::connection("mysql3")->table('pcharacter')->where('Username','=',auth()->user()->username)->get();
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->decrement('credit',$Char->credit); 


            DB::connection("mysql")->table('users')->where('Username','=',$Char->seller)->increment('credit',$Char->credit); 

            if(empty($Slot[0])){
                DB::connection("mysql3")->table('pcharacter')
                ->where('CharID','=',$Char->charid)
                ->update(['Username' => auth()->user()->username,'SlotID' => '1']);

                DB::connection("mysql")->table('market_character')->where('charid','=',$Char->charid)
                ->update(['status' => '3', 'timebuy' => strtotime("now"),'buyer' => auth()->user()->username]);

            }elseif(empty($Slot[1])){
                DB::connection("mysql3")->table('pcharacter')
                ->where('CharID','=',$Char->charid)
                ->update(['Username' => auth()->user()->username,'SlotID' => '2']);

                DB::connection("mysql")->table('market_character')->where('charid','=',$Char->charid)
                ->update(['status' => '3', 'timebuy' => strtotime("now"),'buyer' => auth()->user()->username]);

            }elseif(empty($Slot[2])){
                DB::connection("mysql3")->table('pcharacter')
                ->where('CharID','=',$Char->charid)
                ->update(['Username' => auth()->user()->username,'SlotID' => '3']);

                DB::connection("mysql")->table('market_character')
                ->where('charid','=',$Char->charid)
                ->update(['status' => '3', 'timebuy' => strtotime("now") ,'buyer' => auth()->user()->username]);

            }elseif(empty($Slot[3])){
                return redirect('Market/buy')->with('unsuccess', "No Slot. Please Delete Your Character First");
            }
            
            return redirect()->back()->with('success', "Buy succesfully! .");
    }

    public function buySoloCharacter(Request $request){

                 $Char = MarketCharacter::where('id', '=', $request->id)->firstOrFail();
                 $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

                if (DB::connection("mysql3")->table('pcharacter')->where('Username', '=', auth()->user()->username)->count() >= 3) {
                    // user found
                    return redirect()->back()->with('unsuccess','No More Slot For Character!');
                }


                if($Char->credit > $users->credit){
                    return redirect()->back()->with('unsuccess','Not enough credit!');
                }  

                if($Char->seller == auth()->user()->username){
                    return redirect()->back()->with('unsuccess','Cannot Buy Your Own Sale!');
                }  

                if(!empty($Char->buyer)){
                    if($Char->buyer != auth()->user()->username){
                        return redirect()->back()->with('unsuccess', 'This transfer id is not yours!');
                    }    
                }
                
                if (DB::connection("mysql3")->table('authenticated')->where('CharID', '=', $Char->charid)->count() > 0) {
                    return redirect()->back()->with('unsuccess', "Buy is Online!.");
                }

                if (DB::connection("mysql3")->table('authenticated')->where('Username', '=', auth()->user()->username)->count() > 0) {
                    return redirect()->back()->with('unsuccess', "You Login is Login. Please Log Out First!.");
                }

            $Slot = DB::connection("mysql3")->table('pcharacter')->where('Username','=',auth()->user()->username)->get();
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->decrement('credit',$Char->credit); 


            DB::connection("mysql")->table('users')->where('Username','=',$Char->seller)->increment('credit',$Char->credit); 

            if(empty($Slot[0])){
                DB::connection("mysql3")->table('pcharacter')
                ->where('CharID','=',$Char->charid)
                ->update(['Username' => auth()->user()->username,'SlotID' => '1']);

                DB::connection("mysql")->table('market_character')->where('charid','=',$Char->charid)
                ->update(['status' => '3', 'timebuy' => strtotime("now"),'buyer' => auth()->user()->username]);

            }elseif(empty($Slot[1])){
                DB::connection("mysql3")->table('pcharacter')
                ->where('CharID','=',$Char->charid)
                ->update(['Username' => auth()->user()->username,'SlotID' => '2']);

                DB::connection("mysql")->table('market_character')->where('charid','=',$Char->charid)
                ->update(['status' => '3', 'timebuy' => strtotime("now"),'buyer' => auth()->user()->username]);

            }elseif(empty($Slot[2])){
                DB::connection("mysql3")->table('pcharacter')
                ->where('CharID','=',$Char->charid)
                ->update(['Username' => auth()->user()->username,'SlotID' => '3']);

                DB::connection("mysql")->table('market_character')
                ->where('charid','=',$Char->charid)
                ->update(['status' => '3', 'timebuy' => strtotime("now") ,'buyer' => auth()->user()->username]);

            }elseif(empty($Slot[3])){
                return redirect('Market/buy')->with('unsuccess', "No Slot. Please Delete Your Character First");
            }
            
            return redirect()->back()->with('success', "Buy succesfully! .");
    }

    public function cancelSoloCharacter(Request $request)
    {
        DB::connection("mysql")->table('market_character')->where('id','=',request('id'))->update(['status' => '2','timebuy' => strtotime("now")]);
        return redirect('/Market/buy')->with('unsuccess','Cancel buy character!');
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
