<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ItemDB;
use App\ItemCategory;
use App\Cart;
use App\Boost;
use App\User;
use DB;
use App\CharinvAll;
use App\ItemPurchaseLog;
use App\BoostAccount;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function item($id)
    {   
        $title = "New Item";

        $menuItems = ItemCategory::where('item_mall','=','1')->get();
        //$items = Item::all();
        //$items = \App\ItemCategory::with(['item_category'])->first();
        if($id == '16'){
            $items = ItemDB::with('item_category')->where('newitem','=','1')->get();
        }else{
            $items = ItemDB::with('item_category')->where('item_category_id','=',$id)->get();
        }   
       

        //$itemCategories = ItemCategory::all();

        return view('Store.item',compact('title','items','itemCategories','menuItems'));
    }

    public function store(Request $request)
    {   

        // $items = ItemDB::find($request->id);
        // $items = ItemDB::with('item_category')->where('id','=',$request->id)->get();
        $items = DB::connection("mysql")->table('item')->where('id','=',$request->id)->first();
        // dd($items);
        $cart = new Cart();
        
        $cart->users_id = auth()->user()->id;
        $cart->item_id = $items->id;
        $cart->credit = $items->price;
        $cart->type = $items->type;
        $cart->discount = $items->discount;
        $cart->total = $items->price - ($items->price * ($items->discount / 100));
        $cart->save();


        //return redirect('Store/item/16')->with('success', "".request('item_name')." succesfully added to shopping cart! .");
        return redirect()->back()->with('success', "".request('item_name')." succesfully added to shopping cart! .");

    }

    public function storeBoost(Request $request)
    {

        $cart = new Cart();
        
        $cart->users_id = auth()->user()->id;
        $cart->item_id = request('item_id');
        $cart->credit = request('credit');
        $cart->type = request('type');
        $cart->discount = request('discount');
        $cart->total = $cart->credit - ($cart->credit * ($cart->discount / 100));
        $cart->save();

        return redirect('Store/boost')->with('success', "".request('item_name')." succesfully added to shopping cart! .");

    }

    public function boost()
    {   
        $title = "Boost";
        
        $boosts = Boost::all();

        return view('Store.boost',compact('title','boosts'));
    }

    public function cart()
    {   
         $title = "Cart";

         // type = 1 barang
         // type = 2 boost
         // type = 3 gold
        //$id_slots = DB::connection("mysql3")->table('charinv_all')->where('CharID','=','1073896920')->where('itemID','=','0')->whereBetween('SlotNum', array(86, 133))->first();
        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
        $usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        $boosts = DB::connection("mysql")->table('cart')
                    ->select('cart.*','boost.name','boost.description','boost.exp','boost.credit','boost.discount','boost.time')
                    ->join('boost','boost.id','=','cart.item_id')
                    ->where(['cart.users_id' => auth()->user()->id, 'cart.type' => '2'])
                    ->get();
         
         //$carts = Cart::where('users_id', '=', auth()->user()->id)->where('type','=','1')->orderBy('id', 'DESC')->get();
         $carts = DB::connection("mysql")->table('item')
                    ->join('cart','item.id','=','cart.item_id')
                    ->join('fwworlddevdb.item as db3','item.itemid','=','db3.ItemID')
                    ->where(['users_id' => auth()->user()->id])
                    ->where(['cart.type' => 1])
                    //->orWhere(['market_character.buyer' => auth()->user()->username])
                    ->orderBy('cart.id','DESC')
                    ->get();


         $golds = Cart::where('users_id', '=', auth()->user()->id)->where('type','=','3')->orderBy('date', 'DESC')->get();
         $balance = Cart::where('users_id', '=', auth()->user()->id)->sum('total');

         //dd($balance);

         return view('Store.cart',compact('title','carts','users','balance','usernames','golds','boosts','id_slots'));
    }

    public function storeCart(Request $request)
    {   
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

        if (DB::connection("mysql3")->table('authenticated')->where('Username', '=', auth()->user()->username)->count() > 0) {
           // user found
            return redirect('Store/cart')->with('unsuccess', "Please Log Out First!.");
        }

        if($users->credit <= request('total_all')){
            return redirect('Store/cart')->with('unsuccess', "Not enough credit!.");
        }elseif(empty($request->char_id) || $request->char_id == "NULL"){
            return redirect('Store/cart')->with('unsuccess', "Please Select Your Character Name!.");
        }else{
            //$balance = Cart::where('users_id', '=', auth()->user()->id)->sum('total'); // balance user sekarang
            //$credit = $users->credit - $balance;
            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->decrement('credit',request('total_all'));    // tolak balance credit 

            if(!empty($request->item_cart_id)){ // start untuk beli item
                    $total_item = count($request->item_cart_id); // kira jumlah item dlm cart
                    $id_slots = DB::connection("mysql3")->table('charinv_all')
                                ->where('CharID','=',$request->char_id)
                                ->where('itemID','=','0')
                                ->whereBetween('SlotNum', array(86, 133))
                                ->take($total_item)
                                ->orderBy('SlotNum', 'asc')->get();

                 

                    // $balanceItem = Cart::where('users_id', '=', auth()->user()->id)->count('item_id')->groupBy('item_id');
                    $countCart = DB::table('cart')
                         ->join('item','item.id','=','cart.item_id')
                         ->join('string','string.id','=','item.itemid')
                         ->select('string.value','cart.*','item.itemlimit','item_id', DB::raw('count(*) as total'))
                         ->where('users_id', '=', auth()->user()->id)
                         ->groupBy('item_id')
                         ->get();

                    $countItemCart = count($countCart); // kira semua barang dalam cart


                    for($z = 0; $z < $countItemCart; $z++){ // tolak barang kuantiti
                        echo $countCart[$z]->item_id. ":";
                        echo $total[$z] = $countCart[$z]->itemlimit - $countCart[$z]->total."<br>";
                        if($total[$z] <= '0'){
                            return redirect('Store/cart')->with('unsuccess', $countCart[$z]->value."Out of Stock.");
                        }else{
                            DB::connection("mysql")->table('item')->where('id','=',$countCart[$z]->item_id)->update(['itemlimit' => $total[$z]]);
                        }
                     }
                    

                    for($i = 0; $i < $total_item; $i++){ // untuk item
                        DB::connection("mysql3")->table('charinv_all')
                        ->where('SlotNum',$id_slots[$i]->SlotNum)
                        ->where('CharID',$id_slots[$i]->CharID)
                        ->update(['ItemID' => $request->item_id[$i],'Quantity' => '1','Identified' => $request->identified[$i],'Hardness' => $request->hardness[$i],'Durability' => $request->durability[$i]]); 

                        $itemPuchase = new ItemPurchaseLog(); // save log
                        
                        $itemPuchase->users_id = $request->users_id[$i];
                        $itemPuchase->item_id = $request->item_id[$i];
                        $itemPuchase->credit = $request->credit[$i];
                        $itemPuchase->discount = $request->discount[$i];
                        $itemPuchase->total = $request->total[$i];
                        $itemPuchase->date = date("Y-m-d h:i:sa");

                        $itemPuchase->save();

                        DB::connection("mysql")->table('cart')->where('id', $request->item_cart_id[$i])->where('users_id',$request->users_id[$i])->delete();
                    }
            } // end beli item

            
            if(!empty($request->gold)){ // untuk gold
                $total_gold = count($request->gold_cart_id); // kira gold

                $totalGold = DB::connection("mysql3")->table("pcharstats")->where('CharID','=',$request->char_id)->first(); // cari gold user
                $total_sum_gold = array_sum($request->gold) + $totalGold->CharGold; // kira gold user untuk cart

                for($b = 0; $b < $total_gold; $b++){
                    // tambah gold ke dalam char dan delete brg dlm cart
                    $itemPuchase = new ItemPurchaseLog();
                    
                    $itemPuchase->users_id = $request->users_id[$b];
                    $itemPuchase->item_id = $request->item_id[$b];
                    $itemPuchase->credit = $request->credit[$b];
                    $itemPuchase->discount = $request->discount[$b];
                    $itemPuchase->total = $request->total[$b];
                    $itemPuchase->date = date("Y-m-d");

                    $itemPuchase->save();

                    DB::connection("mysql3")->table('pcharstats')->where('CharID','=',$request->char_id)->update(['CharGold' => $total_sum_gold]);
                    DB::connection("mysql")->table('cart')->where('id', $request->gold_cart_id[$b])->where('users_id',$request->users_id[$b])->delete(); 
                }   
            }  

            if(!empty($request->boostid)){ // untuk boost
                $total_boost = count($request->boostid); // kira gold
                for($c = 0; $c < $total_boost; $c++){
                    $boost = new BoostAccount();
                    
                    $boost->uid = $request->uid[$c];
                    $boost->boostid = $request->boostid[$c];
                    $boost->orixp = $request->orixp[$c];
                    $boost->boostxp = $request->boostxp[$c];
                    $boost->time = $request->time[$c];
                    $boost->charid = $request->char_id;
                    $boost->status = 1;

                    $boost->save();

                    DB::connection("mysql")->table('cart')->where('id', $request->id[$c])->delete(); 
                }   
                    //dd($boost);
            }          
            
            
            

            return redirect('Store/cart')->with('success', "Your Purchase is succesfully.");
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

    public function destroyCartItem($id)
    {
        $cart = Cart::findOrFail($id);

        $cart->delete();

        return redirect('Store/cart')->with('unsuccess', 'Data Have Been Delete!');
    }
    
}
