<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Item;
use App\ItemDB;
use App\ItemCategory;
use App\Boost;
use App\User;
use DB;
use App\News;
use App\Reward;
use App\Download;
use Intervention\Image\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Session;
use Redirect;
//use App\Purchase;

class AddController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function news()
    {   
        $title = "Update New";

        $news = News::all();

        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Admin/Add/news',compact('title','usernames','news'));
    }

    public function boost()
    {   
        $title = "Boost";

        $boosts = DB::connection("mysql")->table('boost')->orderBy('id','ASC')->get();

        // dd($boosts);

        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Admin/Add/boost',compact('title','usernames','boosts'));
    }

    public function addnews()
    {   
        $title = "Add New";

        return view('Admin/Add/addnews',compact('title','usernames'));
    }

    public function addboost()
    {   
        $title = "Add Boost";

        return view('Admin/Add/addboost',compact('title','usernames'));
    }

    public function addItem()
    {   
        $title = "Add New Item";

        return view('Admin/Add/addItem',compact('title'));
    }

    public function storeBoost(Request $request){
        
        $boosts = new Boost();
        
        $boosts->name = $request->get("name");
        $boosts->description = $request->get("description");
        $boosts->exp = $request->get("exp");
        $boosts->credit = $request->get("credit");
        $boosts->discount = $request->get("discount");
        $boosts->time = $request->get("time");
        $boosts->type = 2;
        $boosts->status = 1;

        $boosts->save();

        return redirect('Admin/Add/boost')->with('success', " succesfully added to boost! .");

    }

    public function storeItem(Request $request){

        $items = new Item();
        
        $items->ItemID = $request->get("ItemID");
        $items->Weight = $request->get("Weight");
        $items->LevelGroup = $request->get("LevelGroup");
        $items->BuyPrice = $request->get("BuyPrice");
        $items->PopLimit = $request->get("PopLimit");
        $items->Identify = $request->get("Identify");
        $items->Field1 = $request->get("Field1");
        $items->Field2 = $request->get("Field2");
        $items->Field3 = $request->get("Field3");
        $items->Field4 = $request->get("Field4");
        $items->Field5 = $request->get("Field5");
        $items->Field6 = $request->get("Field6");
        $items->Field7 = $request->get("Field7");
        $items->Field8 = $request->get("Field8");
        $items->Field9 = $request->get("Field9");
        $items->Field10 = $request->get("Field10");
        $items->Field11 = $request->get("Field11");
        $items->Field12 = $request->get("Field12");
        $items->Hardness = $request->get("Hardness");
        $items->SetID = $request->get("SetID");
        $items->PartialSetCount = $request->get("PartialSetCount");
        $items->SetCount = $request->get("SetCount");
        $items->SetEffID1 = $request->get("SetEffID1");
        $items->SetEffID2 = $request->get("SetEffID2");
        $items->SetEffID3 = $request->get("SetEffID3");
        $items->SetEffID4 = $request->get("SetEffID4");
        $items->SetEffID5 = $request->get("SetEffID5");
        $items->SetEffID6 = $request->get("SetEffID6");
        $items->NoTransFlag = $request->get("NoTransFlag");
        $items->MaxLevel = $request->get("MaxLevel");
        $items->ItemSwitchID = $request->get("ItemSwitchID");
        $items->LvlEffID1 = $request->get("LvlEffID1");
        $items->LvlEffID2 = $request->get("LvlEffID2");
        $items->ModEffID1 = $request->get("ModEffID1");
        $items->ModEffID2 = $request->get("ModEffID2");
        $items->LvlAffFlag1 = $request->get("LvlAffFlag1");
        $items->LvlAffFlag2 = $request->get("LvlAffFlag2");
        $items->ModAffFlag1 = $request->get("ModAffFlag1");
        $items->ModAffFlag2 = $request->get("ModAffFlag2");
        $items->LvlDur1 = $request->get("LvlDur1");
        $items->LvlDur2 = $request->get("LvlDur2");
        $items->ModDur1 = $request->get("ModDur1");
        $items->ModDur2 = $request->get("ModDur2");
        $items->XPGain = $request->get("XPGain");
        $items->HeroPrice = $request->get("HeroPrice");
        $items->DecayValue = $request->get("DecayValue");
        $items->DecayRate = $request->get("DecayRate");
        $items->Display = $request->get("Display");
        $items->Release = $request->get("Release");

        $items->save();

    }

    public function reward()
    {   

        $title = "Reward";

        $rewards = DB::connection("mysql")->table('reward_capped')->get();
        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();

        return view('Admin/Add/reward',compact('title','rewards'));
    }

    public function addReward()
    {   
        $title = "Add Reward";

        return view('Admin/Add/addReward',compact('title'));
    }

    public function addDownload()
    {   
        $title = "Update New";

        return view('Admin/Add/addDownload',compact('title'));
    }
   
    public function download()
    {   
        $title = "Download";

        $downloads = DB::connection("mysql")->table('download')->get();
       
        return view('Admin/Add/download',compact('title','usernames','downloads'));
    }

    public function item()
    {   
        $title = "Insert New Item";

        $itemCategories = DB::connection("mysql")->table("item_category")->where('item_mall','=','1')->get();

        return view('Admin/Add/item',compact('title','itemCategories'));
    }

    public function itemCheck(Request $request)
    {   
        $title = "Item Check";

        if($request->id == "")
        {
            
        }
        elseif($request->submit_btn == 'submit')
        {
             //$items = DB::connection("mysql")->table("string")->where('id','LIKE','%'.request('id').'%')->orWhere('value','LIKE','%'.request('id').'%')->get();
            $items = DB::connection("mysql3")->table('item')
            ->where('id','LIKE','%'.request('id').'%')
            ->orWhere('value','LIKE','%'.request('id').'%')
            ->join('DB.string as db2','item.ItemID','=','db2.id')
            ->get();
        }else{
            $items = DB::connection("mysql")->table("string")->where('value','=','ayam')->get();
        }

        //$items = DB::connection("mysql")->table("string")->where('id','!=','5244578')->get();
        
        return view('Admin/Add/itemCheck',compact('title','items'));
    }
   
    public function itemDetail($id)
    {   
        $title = "Item Detail";

        $getItemnpcs = DB::connection("mysql3")->table('treasure')
            //->Leftjoin('npcattrib','npcattrib.AttribID','=','treasure.Indx')
            ->Where('Item1','=',$id)
            ->get();

        $itemNames = DB::connection("mysql")->table("string")->where('id','=',$id)->first();

        $itemDetails = DB::connection("mysql3")->table("item")->where('ItemID','=',$id)->first();


        return view('Admin/Add/itemDetail',compact('title','itemNames','itemDetails','getItemnpcs'));
    }

    public function updateItemDetail(Request $request, $id){
        
        $items = Item::where('ItemID', '=', $id)->first();


        $items->Weight = $request->get("Weight");
        $items->LevelGroup = $request->get("LevelGroup");
        $items->BuyPrice = $request->get("BuyPrice");
        $items->PopLimit = $request->get("PopLimit");
        $items->Identify = $request->get("Identify");
        $items->Field1 = $request->get("Field1");
        $items->Field2 = $request->get("Field2");
        $items->Field3 = $request->get("Field3");
        $items->Field4 = $request->get("Field4");
        $items->Field5 = $request->get("Field5");
        $items->Field6 = $request->get("Field6");
        $items->Field7 = $request->get("Field7");
        $items->Field8 = $request->get("Field8");
        $items->Field9 = $request->get("Field9");
        $items->Field10 = $request->get("Field10");
        $items->Field11 = $request->get("Field11");
        $items->Field12 = $request->get("Field12");
        $items->Hardness = $request->get("Hardness");
        $items->SetID = $request->get("SetID");
        $items->PartialSetCount = $request->get("PartialSetCount");
        $items->SetCount = $request->get("SetCount");
        $items->SetEffID1 = $request->get("SetEffID1");
        $items->SetEffID2 = $request->get("SetEffID2");
        $items->SetEffID3 = $request->get("SetEffID3");
        $items->SetEffID4 = $request->get("SetEffID4");
        $items->SetEffID5 = $request->get("SetEffID5");
        $items->SetEffID6 = $request->get("SetEffID6");
        $items->NoTransFlag = $request->get("NoTransFlag");
        $items->MaxLevel = $request->get("MaxLevel");
        $items->ItemSwitchID = $request->get("ItemSwitchID");
        $items->LvlEffID1 = $request->get("LvlEffID1");
        $items->LvlEffID2 = $request->get("LvlEffID2");
        $items->ModEffID1 = $request->get("ModEffID1");
        $items->ModEffID2 = $request->get("ModEffID2");
        $items->LvlAffFlag1 = $request->get("LvlAffFlag1");
        $items->LvlAffFlag2 = $request->get("LvlAffFlag2");
        $items->ModAffFlag1 = $request->get("ModAffFlag1");
        $items->ModAffFlag2 = $request->get("ModAffFlag2");
        $items->LvlDur1 = $request->get("LvlDur1");
        $items->LvlDur2 = $request->get("LvlDur2");
        $items->ModDur1 = $request->get("ModDur1");
        $items->ModDur2 = $request->get("ModDur2");
        $items->XPGain = $request->get("XPGain");
        $items->HeroPrice = $request->get("HeroPrice");
        $items->DecayValue = $request->get("DecayValue");
        $items->DecayRate = $request->get("DecayRate");
        $items->Display = $request->get("Display");
        $items->Release = $request->get("Release");


        $items->save();

        return Redirect::back()->with('success', 'Item Updated!');
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

        $news = new News();
        
        $news->by = auth()->user()->id;
        $news->title = request('title');
        $news->news = request('news');
        $news->date = date("Y-m-d");

        $news->save();

        return redirect('Admin/Add/news')->with('success', "".request('item_name')." succesfully added to news! .");

    }

    public function storeReward(Request $request)
    {
        if (DB::connection("mysql")->table('reward_capped')->count() >= 4) {
           // user found
            return redirect('Admin/Add/reward')->with('unsuccess', "Only 5 Item Reward!.");
        }

        $rewards = new Reward();
        
        $rewards->item = request('item');
        $rewards->description = request('description');
        $rewards->level = request('level');
        $rewards->ft = request('ft');
        $rewards->item_quantity = request('item_quantity');

        $rewards->save();

        return redirect('Admin/Add/reward')->with('success', "".request('description')." succesfully added to news! .");

    }

    public function storeDownload(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'link'=>'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        

        if ($request->hasFile('picture')) {
            $download = new Download();
            $image = $request->file('picture');
            $picture = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/picture');
            $image->move($destinationPath, $picture);
            $download->picture = $picture;
            $download->name =  $request->get('name');
            $download->link = $request->get('link');
            $download->updated_at = date("Y-m-d H:m:s");
            $download->save();

        return redirect('Admin/Add/download')->with('success', "succesfully added to Item Mall! .");
        }

    }

    public function storeItemMall(Request $request)
    {

        $itemDetails = DB::connection("mysql")->table("item")->get();

        if (DB::connection("mysql")->table("item")->where('itemid', '=', request('itemid'))->orwhere('name', '=', request('name'))->count() > 0) {
           // user found
            return redirect('Admin/Add/item')->with('unsuccess', "Item Already in Shop! .");
        }
           $this->validate($request, [
               'itemid' => 'required',
               'name'=>'required',
               'level'=>'required',
               'item_category_id'=>'required',
               'type'=>'required',
               'price'=>'required',
               'discount'=>'required',
               'itemlimit'=>'required',
               'newitem'=>'required',
               'status'=>'required',
               'description'=>'required',
               'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           ]);

           

           if ($request->hasFile('icon')) {
               $item = new ItemDB();
               $image = $request->file('icon');
               $icon = time().'.'.$image->getClientOriginalExtension();
               $destinationPath = public_path('/item_img');
               $image->move($destinationPath, $icon);
               $item->icon = $icon;
               $item->itemid =  $request->get('itemid');
               $item->name = $request->get('name');
               $item->level =  $request->get('level');
               $item->item_category_id = $request->get('item_category_id');
               $item->type =  $request->get('type');
               $item->price = $request->get('price');
               $item->discount =  $request->get('discount');
               $item->itemlimit = $request->get('itemlimit');
               $item->quantity = $request->get('quantity');
               $item->newitem =  $request->get('newitem');
               $item->status = $request->get('status');
               $item->description =  $request->get('description');
               $item->updated_at = date("Y-m-d H:m:s");
               $item->save(); 
        }

        

        return redirect('Admin/Add/item')->with('success', "Succesfully added to Item Mall! .");
        

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
        $new = News::find($id);
        return view('Admin.Add.edit', compact('new'));
    }

    public function editBoost($id)
    {
        //
        $boost = Boost::find($id);
        return view('Admin.Add.editBoost', compact('boost'));
    }

    public function editReward($id)
    {
        //
        $reward = Reward::find($id);
        return view('Admin.Add.editReward', compact('reward'));
    }

    public function editDownload($id)
    {
        //
        $download = Download::find($id);
        return view('Admin.Add.editDownload', compact('download'));
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
            'title' => 'required',
            'news'=>'required',
        ]);

        $new = News::find($id);
        $new->title =  $request->get('title');
        $new->news = $request->get('news');
        $new->updated_at = date("Y-m-d H:m:s");
        $new->save();

        
        return redirect('Admin/Add/news')->with('success', 'News updated!');
    }

    public function updateBoost(Request $request, $id)
    {
        
        $this->validate($request, [
            'name' => 'required',
            'description'=>'required',
        ]);

        $boost = Boost::find($id);
        $boost->name =  $request->get('name');
        $boost->description = $request->get('description');
        $boost->exp =  $request->get('exp');
        $boost->credit = $request->get('credit');
        $boost->discount =  $request->get('discount');
        $boost->time = $request->get('time');
        $boost->save();

        
        return redirect('Admin/Add/boost')->with('success', 'Boost updated!');
    }

    public function updateReward(Request $request, $id)
    {
        
        $this->validate($request, [
            'item' => 'required',
            'description'=>'required',
        ]);

        $reward = Reward::find($id);
        $reward->item =  $request->get('item');
        $reward->description = $request->get('description');
        $reward->level =  $request->get('level');
        $reward->ft = $request->get('ft');
        $reward->item_quantity = $request->get('item_quantity');
        $reward->save();

        return redirect('Admin/Add/reward')->with('success', 'Reward updated!');
    }

    public function updateDownload(Request $request, $id)
    {
       

        $this->validate($request, [
            'name' => 'required',
            'link'=>'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        

        if ($request->hasFile('picture')) {
            $download = Download::find($id);
            $image = $request->file('picture');
            $picture = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/picture');
            $image->move($destinationPath, $picture);
            $download->picture = $picture;
            $download->name =  $request->get('name');
            $download->link = $request->get('link');
            $download->updated_at = date("Y-m-d H:m:s");
            $download->save();

            return redirect('Admin/Add/download')->with('success', 'News updated!');
        }else{
            //return redirect('Admin/Add/download')->with('success', 'Please Insert Picture!');
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $new = News::findOrFail($id);

        $new->delete();

        return redirect('Admin/Add/news')->with('unsuccess', 'Data Have Been Delete!');
    }

    public function destroyReward($id)
    {
        $reward = Reward::findOrFail($id);

        $reward->delete();

        return redirect('Admin/Add/reward')->with('unsuccess', 'Data Have Been Delete!');
    }

    public function destroyDownload($id)
    {
        $download = Download::findOrFail($id);

        $download->delete();

        return redirect('Admin/Add/download')->with('unsuccess', 'Data Have Been Delete!');
    }

    public function destroyBoost($id)
    {
        $boosts = Boost::findOrFail($id);

        $boosts->delete();

        return redirect('Admin/Add/boost')->with('unsuccess', 'Data Have Been Delete!');
    }

    public function upload(Request $request){
        dd($request);
        }
}

