<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deposit;
use App\WireDepositRequest;
use App\Transaction;
use Validator;
use Illuminate\Validation\Rule;
use Auth;
use DB;

class DepositController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone'));
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$user_type = Auth::user()->user_type;

        $deposits = Deposit::all()->sortByDesc("id");
        return view('backend.deposit.list',compact('deposits'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.deposit.create');
		}else{
           return view('backend.deposit.modal.create');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        @ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$validator = Validator::make($request->all(), [
			'type' => 'required',
			'amount' => 'required|numeric',
			'account_id' => 'required',
			'user_id' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('deposit.create')
							->withErrors($validator)
							->withInput();
			}			
		}
			

		DB::beginTransaction();

        $deposit = new Deposit();
	    $deposit->method = 'Manual';
	    $deposit->type = $request->input('type');
		$deposit->amount = $request->input('amount');
		$deposit->account_id = $request->input('account_id');
		$deposit->note = $request->input('note');
		$deposit->status = 1;
		$deposit->user_id = $request->input('user_id');
	
        $deposit->save();
		
		//Create Transaction
		$transaction = new Transaction();
	    $transaction->user_id = $request->input('user_id');
		$transaction->amount = $request->input('amount');
		$transaction->account_id = $request->input('account_id');
		$transaction->dr_cr = 'cr';
		$transaction->type = $request->input('type');
		$transaction->status = 'complete';
		$transaction->note = $request->input('note');
		$transaction->ref_id = $deposit->id;
		$transaction->created_by = Auth::user()->id;
		$transaction->updated_by = Auth::user()->id;
	
        $transaction->save();

		
		//Registering Event
		//event(new \App\Events\DepositMoney($transaction));


		DB::commit();
		
		//Prefix Output
		if($deposit->status == 0){
			$deposit->status = "<span class='badge badge-warning'>"._lang('Pending')."</span>";
		}else if($deposit->status == 1){
			$deposit->status = "<span class='badge badge-success'>"._lang('Completed')."</span>";
		}else if($deposit->status == 2){
			$deposit->status = "<span class='badge badge-danger'>"._lang('Canceled')."</span>";
		}
		
		$deposit->type = ucwords(str_replace('_',' ',$deposit->type));
		$deposit->account_id = $deposit->account->account_number;
		$deposit->user_id = $deposit->user->first_name.' '.$deposit->user->last_name;
		$deposit->amount = decimalPlace($deposit->amount);
		
		
        
		if(! $request->ajax()){
           return redirect()->route('deposit.create')->with('success', _lang('Deposit made sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Deposit made sucessfully'),'data'=>$deposit]);
		}
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $deposit = Deposit::find($id);
		if(! $request->ajax()){
		    return view('backend.deposit.view',compact('deposit','id'));
		}else{
			return view('backend.deposit.modal.view',compact('deposit','id'));
		} 
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $deposit = Deposit::find($id);
		if(! $request->ajax()){
		   return view('backend.deposit.edit',compact('deposit','id'));
		}else{
           return view('backend.deposit.modal.edit',compact('deposit','id'));
		}  
        
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

    	@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		$validator = Validator::make($request->all(), [
			'method' => '',
			'amount' => 'required|numeric',
			'account_id' => 'required',
			'status' => 'required',
			'user_id' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('deposit.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		DB::beginTransaction();

        $deposit = Deposit::find($id);
		//$deposit->method = $request->input('method');
		$deposit->amount = $request->input('amount');
		$deposit->account_id = $request->input('account_id');
		$deposit->note = $request->input('note');
		$deposit->status = $request->input('status');
		$deposit->user_id = $request->input('user_id');
	
        $deposit->save();
		
		//Update Transaction
		$transaction = Transaction::where('ref_id',$id)
								  ->where('type','deposit')->first();
		$transaction->user_id = $request->input('user_id');
		$transaction->amount = $request->input('amount');
		$transaction->account_id = $request->input('account_id');
		$transaction->dr_cr = 'cr';
		$transaction->type = 'deposit';
		
		if($request->input('status') == 0){
			$transaction->status = 'pending';
		}else if($request->input('status') == 1){
			$transaction->status = 'complete';
		}else if($request->input('status') == 2){
			$transaction->status = 'cancel';
		}
		
		$transaction->note = $request->input('note');
		$transaction->updated_by = Auth::id();
	
        $transaction->save();

        DB::commit();
		
		//Prefix Output
		if($deposit->status == 0){
			$deposit->status = "<span class='badge badge-warning'>"._lang('Pending')."</span>";
		}else if($deposit->status == 1){
			$deposit->status = "<span class='badge badge-success'>"._lang('Completed')."</span>";
		}else if($deposit->status == 2){
			$deposit->status = "<span class='badge badge-danger'>"._lang('Canceled')."</span>";
		}
		
		$deposit->account_id = $deposit->account->account_number;
		$deposit->user_id = $deposit->user->first_name.' '.$deposit->user->last_name;
		$deposit->amount = decimalPlace($deposit->amount);
		
		
		if(! $request->ajax()){
           return redirect()->route('deposit.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$deposit]);
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
    	if(Auth::user()->user_type != 'admin'){
    		return back()->with('error',_lang('Permission denied !'));
    	}
    	
    	DB::beginTransaction();
		//Delete Deposit
        $deposit = Deposit::find($id);
        $deposit->delete();
		
		//Delete Transaction
		$transaction = Transaction::where('ref_id',$id)
		                          ->where('type','!=','withdraw');
		$transaction->delete();
		DB::commit();
		
        return redirect()->route('deposit.index')->with('success',_lang('Deleted Sucessfully'));
    }
}
