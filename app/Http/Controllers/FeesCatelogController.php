<?php

namespace App\Http\Controllers;

use App\FeeInfo;
use App\FeesCatelog;
use App\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeesCatelogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$title = _lang('Fees Catalog');

		$fees = FeesCatelog::orderBy('id', 'DESC')->get();

		return view('backend.fees-catalog.listing', compact('title', 'fees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $transCategory = TransactionCategory::latest()->get();
        $title = _lang('Fees Catalog');
        return view('backend.fees-catalog.create', compact('title', 'transCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fee_id' => 'required',
                'feeName' => 'required',
                'feeDescription' => 'required',
                'feeDetialDescription' => 'required',
                'charge_type' => 'required',
                'fee_type' => 'required',
                'unit_of_measure' => 'required',
                'payer' => 'required',
                'sender_pay' => 'required_if:payer,' . FeeInfo::SPLIT,
                'receiver_pay' => 'required_if:payer,' . FeeInfo::SPLIT,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "data" => "Please fill all required fields."
                ], 400);
            }

            $oldFeesId = FeesCatelog::where('fees_id', $request->fee_id)->first();
            if($oldFeesId){
                return response()->json([
                    "success" => false,
                    "data" => "Fees Id already exists!"
                ], 400);
            }

            $senderPay = ($request->payer !== FeeInfo::SPLIT) ? 0 : $request->sender_pay;
            $receiverPay = ($request->payer !== FeeInfo::SPLIT) ? 0 : $request->receiver_pay;

            $feesCatelog = new FeesCatelog();
            $feesCatelog->fees_id = $request->fee_id;
            $feesCatelog->name = $request->feeName;
            $feesCatelog->description = $request->feeDescription;
            $feesCatelog->detailed_description = $request->feeDetialDescription;
            $feesCatelog->charges_type = $request->charge_type;
            $feesCatelog->transaction_id = $request->fee_type;
            $feesCatelog->unit_of_measure = $request->unit_of_measure;
            $feesCatelog->payer = $request->payer;
            $feesCatelog->sender_pay = $senderPay;
            $feesCatelog->receiver_pay = $receiverPay;
            $feesCatelog->save();

            return response()->json([
                "success" => true,
                "data" => "Fees Catalog saved successfully!"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "data" => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\FeesCatelog  $feesCatelog
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fee = FeesCatelog::where('id',$id)->with('transactionCategory')->first();

		return view('backend.fees-catalog.modal.view',compact('fee','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FeesCatelog  $feesCatelog
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = _lang('Edit Fees Catalog');

        $fee = FeesCatelog::where('id',$id)->with('transactionCategory')->first();
        $transCategory = TransactionCategory::latest()->get();
        if($fee) {
            return view('backend.fees-catalog.edit',compact('fee','id', 'title', 'transCategory'));
        }

        return redirect()->back()->with('error', _lang('Fees catalog doesn\'t exist!'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FeesCatelog  $feesCatelog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fee_id' => 'required|unique:fees_catelogs,fees_id,' . $id,
                'feeName' => 'required',
                'feeDescription' => 'required',
                'feeDetialDescription' => 'required',
                'charge_type' => 'required',
                'fee_type' => 'required',
                'unit_of_measure' => 'required',
                'payer' => 'required',
                'sender_pay' => 'required_if:payer,' . FeeInfo::SPLIT,
                'receiver_pay' => 'required_if:payer,' . FeeInfo::SPLIT,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "data" => "Please fill all required fields."
                ], 400);
            }

            $feesCatelog = FeesCatelog::find($id);
            if (!$feesCatelog) {
                return response()->json([
                    "success" => false,
                    "data" => "Fee catalog not found!"
                ], 404);
            }

            $senderPay = ($request->payer !== FeeInfo::SPLIT) ? 0 : $request->sender_pay;
            $receiverPay = ($request->payer !== FeeInfo::SPLIT) ? 0 : $request->receiver_pay;

            $feesCatelog->fees_id = $request->fee_id;
            $feesCatelog->name = $request->feeName;
            $feesCatelog->description = $request->feeDescription;
            $feesCatelog->detailed_description = $request->feeDetialDescription;
            $feesCatelog->charges_type = $request->charge_type;
            $feesCatelog->transaction_id = $request->fee_type;
            $feesCatelog->unit_of_measure = $request->unit_of_measure;
            $feesCatelog->payer = $request->payer;
            $feesCatelog->sender_pay = $senderPay;
            $feesCatelog->receiver_pay = $receiverPay;
            $feesCatelog->save();

            return response()->json([
                "success" => true,
                "data" => "Fees Catalog updated successfully!"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "data" => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FeesCatelog  $feesCatelog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $fee = FeesCatelog::where('id', $id)->first();

        if($fee) {
            $fee->delete();
            return redirect('admin/fees-catalog')->with('success',_lang('Removed Sucessfully'));
        } else {
            return redirect('admin/fees-catalog')->withErrors(_lang('Something went wrong!'));
        }
    }

    public function getFeesCatalogs(Request $request){
        try {
            $ids = $request->ids;
            $selectedFeesCatalogs = FeesCatelog::with('transactionCategory')->whereIn("id", $ids)->get();
            return response()->json([
                "success" => true,
                "data" => $selectedFeesCatalogs
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "data" => $th->getMessage()
            ]);
        }
    }

}
