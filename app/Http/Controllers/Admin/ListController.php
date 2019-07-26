<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use App\User3;
use DB;
use App\News;
use App\Pcharacter;
use App\ItemCategory;
use App\Item;
use App\ItemDB;
//use App\Purchase;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function player()
    {   
        $title = "List Account PLayer";

        $users = DB::table("users")->where('group','!=','1')->get();

        return view('Admin/List/player',compact('title','users'));
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

    public function item($id)
    {
        $title = "Store Updating";

        $menuItems =  ItemCategory::where('item_mall','=','1')->get();
            if($id == '16'){
                $items = ItemDB::with('item_category')->where('newitem','=','1')->get();
            }else{
                $items = ItemDB::with('item_category')->where('item_category_id','=',$id)->get();
            }  

        return view('Admin.List.item',compact('title','items','itemCategories','menuItems'));
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
        // $purchase = new Purchase();
 
        // $purchase->name = request('name');
        // $purchase->code = request('code');
        // $purchase->code_pen = request('code_pen');
        // $purchase->desc = request('desc');

 
        // $purchase->save();
        
        // return redirect('/Purchase')->with('success','Add successfully');;

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
        $users = DB::table("users")->find($id);
        return view('Admin.List.edit', compact('users'));
    }

    public function editItemMall($id)
    {

        $items = DB::table("item")->find($id);
        $itemCategories = DB::connection("mysql")->table("item_category")->where('item_mall','=','1')->get();

        return view('Admin.List.editItemMall', compact('items','itemCategories'));
    }


    public function updateItemMall(Request $request, $id)
    {
        $this->validate($request, [
            'itemid' => 'required',
            'name'=>'required',
            //'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

            $item = ItemDB::find($id);
            
            if(empty($request->icon) || $request->icon == "NULL"){

            }else{
                $image = $request->file('icon');
                $icon = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/item_img');
                $image->move($destinationPath, $icon);
                $item->icon = $icon;
            }
                
            
            
            
            $item->itemid =  $request->get('itemid');
            $item->name = $request->get('name');
            $item->level = $request->get('level');
            $item->item_category_id = $request->get('item_category_id');
            $item->type = $request->get('type');
            $item->price = $request->get('price');
            $item->description = $request->get('description');
            $item->discount = $request->get('discount');
            $item->itemlimit = $request->get('itemlimit');
            $item->newitem = $request->get('newitem');
            $item->status = $request->get('status');
            $item->quantity = $request->get('quantity');
            $item->updated_at = date("Y-m-d H:m:s");

            $item->save();
            //return redirect()->back()->with('success', "succesfully update! .");
            return redirect('Admin/List/item/'.$request->get('item_category_id'))->with('success', 'Item Mall updated!');  
        
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
        
        $this->validate($request, [
            'username' => 'required',
            'name'=>'required',
        ]);

         $user = User::find($id);

        $user->username =  $request->get('username');
        $user->name = $request->get('name');
        $user->group = $request->get('group');
        $user->email = $request->get('email');
        $user->credit = $request->get('credit');
        $user->ft = $request->get('ft');
        $user->address1 = $request->get('address1');
        $user->address2 = $request->get('address2');
        $user->postcode = $request->get('postcode');
        $user->country = $request->get('country');
        $user->state = $request->get('state');
        $user->updated_at = date("Y-m-d H:m:s");

        $user->save();

        return redirect('Admin/List/player')->with('success', 'Info Player updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = User::findOrFail($id);

        $users->delete();

        return redirect('Admin/List/player')->with('unsuccess', 'Data Have Been Delete!');
    }

    public function destroyItemMall($id)
    {
        $items = ItemDB::findOrFail($id);

        $items->delete();

        return redirect()->back()->with('unsuccess', "Data Have Been Delete! .");

        //return redirect('Admin/List/player')->with('unsuccess', 'Data Have Been Delete!');
    }
}
