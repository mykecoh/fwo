<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Item;
use App\ItemCategory;
use App\Boost;
use App\User;
use App\CreditTransferLog;
use App\ChangenickHistory;
use DB;
//use App\Purchase;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PurchaseItem()
    {   
        $title = "Log Purchase Item";

        $purchaseLogs = DB::connection("mysql")->table('item_purchase_log')
            ->select(['item_purchase_log.*','users.id','users.username','string.value'])
            ->join('users','users.id','=','item_purchase_log.users_id')
            ->join('string','string.id','=','item_purchase_log.item_id')
            ->orderBy('date','desc')
            ->get();

        return view('Admin/Log/PurchaseItem',compact('title','purchaseLogs'));
    }
   
    public function PurchaseCredit()
    {   
        $title = "Log Purchase Credit";

        $payments = DB::connection("mysql")->table('payments')
            ->select(['payments.*','users.id','users.name','credit_game.value'])
            ->join('credit_game','credit_game.value','=','payments.amount')
            ->join('users','users.id','=','payments.user_id')
            ->orderBy('payments.date','desc')
            ->get();

        return view('Admin/Log/PurchaseCredit',compact('title','payments'));
    }

    public function TransferCredit()
    {   
        $title = "Log Transfer Credit";

        $credits = CreditTransferLog::orderBy('date', 'DESC')->get();

        return view('Admin/Log/TransferCredit',compact('title','credits'));
    }

    public function Unstuck()
    {   
        $title = "Log Unstuck Reset";

        //$usernames = DB::connection("mysql3")->table("pcharacter")->where('Username','=',auth()->user()->username)->get();
        $unstucks = $purchaseLogs = DB::connection("mysql")->table('unstuck_log')
            ->join('fwworlddevdb.pcharacter as db2','unstuck_log.charid','=','db2.CharID')->get();

        return view('Admin/Log/Unstuck',compact('title','unstucks'));
    }
    
    public function ChangeNickname()
    {   
        $title = "Log Change Nickname";

        $nicknames = ChangenickHistory::orderBy('date', 'DESC')->get();

        return view('Admin/Log/ChangeNickname',compact('title','nicknames'));
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
