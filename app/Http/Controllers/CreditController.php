<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
//use App\Purchase;
use App\User;
use App\Payment;
use App\CreditTransferLog;
use App\User3;
use DB;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchase()
    {   
         $title = "Purchase";
         //$purchase = Purchase::all();

         $credits = DB::connection("mysql")->table('credit_game')->get();

         return view('Credit.purchase',compact('purchase','title','credits'));
    }

    public function transfer()
    {   
         $title = "Transfer";

         $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();

         $credits = CreditTransferLog::where('from', '=', auth()->user()->username)->orderBy('date', 'DESC')->get();

         $creditsFroms = CreditTransferLog::where('transfer_to', '=', auth()->user()->username)->orderBy('date', 'DESC')->get();

         return view('Credit.transfer',compact('title','users','credits','creditsFroms'));
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
        

        $users = DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->first();
        //dd($users->credit);
        if (!User::where('username', request('username'))->first()){
            return redirect('Credit/transfer')->with('unsuccess', "User does not exists!.");
        }elseif($users->credit <= request('credit')){
            return redirect('Credit/transfer')->with('unsuccess', "Not enough credit!.");
        }elseif($request->username == auth()->user()->username){
            return redirect('Credit/transfer')->with('unsuccess', "Cannot transfer to self!.");
        }else{
            $Credit_transfer_log = new CreditTransferLog();
            
            $Credit_transfer_log->from = auth()->user()->username;
            $Credit_transfer_log->transfer_to = request('username');
            $Credit_transfer_log->amount = request('credit');
            $Credit_transfer_log->save();

            DB::connection("mysql")->table('users')->where('id','=',auth()->user()->id)->decrement('credit',request('credit'));
            DB::connection("mysql")->table('users')->where('username','=',request('username'))->increment('credit',request('credit'));
            return redirect('Credit/transfer')->with('success', "Your Credit Has Been Transferred.");
        }

    }


    public function storeCredit(Request $request)
    {
        $user = auth()->user();
        $rand = mt_rand();
        //$payInfo = Payment::where('id', '=', $request->id)->firstOrFail();
        $payInfo = DB::connection("mysql")->table('credit_game')->where('id','=',$request->id)->first();
        $amount = $payInfo->value;
        

        $some_data = array(
            'userSecretKey' => 'hmihgoui-vn93-ront-i4y3-322gwyxjijmp',
            'categoryCode' => 'ks6mtl9l',
            'billName' => 'Pembelian Credit',
            'billDescription' => 'Credit',
            'billPriceSetting'=>0,
            'billPayorInfo'=>1,
            'billAmount'=> $amount, //value berapa nok cas dia
            'billReturnUrl'=>'https://fwclans.com/Credit/storeCredit/lkrqcatujz', // url selepas success/failed buat payment (redirect) kembali
            'billCallbackUrl'=>'https://fwclans.com/Credit/storeCredit/ywpynygzkg', // url selepas success/failed buat payment (tapi SERVER PAYMENTE GATEWAY send POST data ke URL ni) panggil
            'billExternalReferenceNo' => $rand,
            'billTo'=> $user->name,
            'billEmail'=> $user->email,
            'billPhone'=>$user->phone,
            'billSplitPayment'=>0,
            'billSplitPaymentArgs'=>'',
            'billPaymentChannel'=>0

          );  

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');  
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

          $result = curl_exec($curl);
          $info = curl_getinfo($curl);  
          curl_close($curl);
          $obj = @json_decode($result, true);

          if(is_array($obj)) {
            $billCode = $obj[0]['BillCode'];

            $payment = new Payment;
            $payment->user_id = $user->id;
            $payment->amount = $amount;
            $payment->amount_receive = 0;
            $payment->bill_code = $billCode;
            $payment->status = 2; // 1 = payment success, 2 = pending, 3 = unsuccessfully
            $payment->save();

            $url = 'https://toyyibpay.com/'.$billCode;
            return redirect($url);
          } else {
            dd('Create new bill failed');
          }
    }

    public function YUERHBITKS(Request $request)
    { // ni xpe org tgk kembali
        //dd('Selamat datang', $request);
        if($request->status_id == 1) {
            return redirect('Credit/purchase')->with('success', "Successful Topup");
        }elseif($request->status_id == 2){
            return redirect('Credit/purchase')->with('unsuccess', "Your Credit Has Been Transferred.");
        }else{
            return redirect('Credit/purchase')->with('unsuccess', "Fail Topup.");
        }
    }

    public function LSNMEBKHOT(Request $request)
    {
        
        // panggil
        $payment = Payment::where(['bill_code' => $request->billcode]);
        //$payment = DB::connection("mysql")->table('payments')->where('bill_code','=',$request->billcode);
        if($payment->count() < 0) {
            return response()->json(['msg' => 'Bill code not found']);
        }

        $payment = $payment->first();
        $amount = $payment->amount;
        
        if($payment->status == 1) {
            //payment dah berjaya buat sebelum ni, jgn buat apa
        } else {
            
            if($request->status == 1) {
                // sini nk test
                //buat masuk kredit dlm akaun charaters
                //DB::connection("mysql")->table('users')->increment('credit', $amount, ['id' => $payment->user_id]);
                DB::connection("mysql")->table('users')->where('id','=',$payment->user_id)->increment('credit',$amount);
            }
            $payment->amount_receive = $amount;
            $payment->status = $request->status;
            $payment->order_id = $request->order_id;
            $payment->reason = $request->reason;
            $payment->save();
            return response()->json(['msg' => 'Done']); 
        }
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
