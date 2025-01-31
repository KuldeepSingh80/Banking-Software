<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\FeeInfo;
use App\FeeSharing;
use App\PartnerFeeSharing;
use App\Http\Requests\SaveFeeRequest;
use App\Http\Resources\FeeDetailCollection;
use App\Merchant;
use App\Partner;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = _lang('Fee Management');
        $partners = Partner::latest()->get();
        $merchants = Merchant::latest()->get();
        return view('backend.administration.fee_management.create', compact('title', 'partners', 'merchants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        $fee = FeeInfo::where('id',$id)->with('feeSharing', 'feeSharing.partnerFeeSharing')->first();

		return view('backend.administration.fee_management.modal.view',compact('fee','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = _lang('Edit Fee');

        $fee = FeeInfo::where('id',$id)->with('feeSharing', 'feeSharing.partnerFeeSharing', 'feeSharing.partnerFeeSharing.partners')->first();
        $feeSharingIds = $fee->feeSharing->pluck('id');
        $sharingPartner = PartnerFeeSharing::whereIn('sharing_level_id', $feeSharingIds)->with('partners')->groupBy('partner_id')->get();
        $partners = Partner::latest()->get();
        $merchants = Merchant::latest()->get();
        if($fee) {
            return view('backend.administration.fee_management.edit',compact('fee','id', 'title', 'partners', 'merchants', 'sharingPartner'));
        }

        return redirect()->back()->with('error', _lang('Fee doesn\'t exist!'));
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
        $fee = FeeInfo::where('id', $id)->first();

        if($fee) {
            $fee->delete();
            return redirect('admin/administration/fee_management')->with('success',_lang('Removed Sucessfully'));
        } else {
            return redirect('admin/administration/fee_management')->withErrors(_lang('Something went wrong!'));
        }
    }

    
	public function save_fee(SaveFeeRequest $request)
	{
		$data = $request->all();

		$data = $request->validated();

        DB::beginTransaction();

        try {
            if($data['payer'] !== FeeInfo::SPLIT) {
                $data['sender_pay'] = 0;
                $data['receiver_pay'] = 0;
            }
            $data['partners'] = count($request->partners);
            $fee = FeeInfo::create($data);

            foreach ($data['levels_data'] as $level) {
                $feeSharing = FeeSharing::create([
                    'fee_id' => $fee->id,
                    'sharing_level' => $level['level_index'],
                    'fixed_base_cost' => $level['base_fixed'],
                    'percentage_base_cost' => $level['base_percentage'],
                    'fixed_markup' => $level['fixed_markup_cost'],
                    'percentage_markup' => $level['percentage_markup_cost'],
                    'fixed_markup_base_cost' => $level['fixed_markup_base_cost'],
                    'percentage_markup_base_cost' => $level['percentage_markup_base_cost'],
                ]);

                foreach ($level['partners'] as $partner) {
                    PartnerFeeSharing::create([
                        'sharing_level_id' => $feeSharing->id,
                        'partner_id' => $partner['partner_id'],
                        'sharing' => $partner['sharing'] ?: 0,
                        'fixed_cost' => $partner['fixed_share'] ?: 0,
                        'percentage_cost' => $partner['percentage_share'] ?: 0,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['result' => 'success', 'message' => 'Fee created successfully.']);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['result' => 'error', 'message' => $th->getMessage()], 500);
        }
	}

	public function update_fee(SaveFeeRequest $request, $id)
	{
		$fee = FeeInfo::where('id', $id)->first();
		if (!$fee) {
			return response()->json(['result' => 'error', 'message' => 'Fee doesn\'t exist!'], 404);
		}

		$data = $request->validated();
        if($data['payer'] !== FeeInfo::SPLIT) {
            $data['sender_pay'] = 0;
            $data['receiver_pay'] = 0;
        }

		DB::beginTransaction();

		try {
			$feeSharingRecords = FeeSharing::where('fee_id', $id)->get();

			foreach ($feeSharingRecords as $feeSharing) {
				PartnerFeeSharing::where('sharing_level_id', $feeSharing->id)->delete();
			}

			FeeSharing::where('fee_id', $id)->delete();

            $data['partners'] = count($request->partners);
			$fee->update($data);
			foreach ($data['levels_data'] as $level) {
				$feeSharing = FeeSharing::create([
					'fee_id' => $fee->id,
					'sharing_level' => $level['level_index'],
					'fixed_base_cost' => $level['base_fixed'],
					'percentage_base_cost' => $level['base_percentage'],
					'fixed_markup' => $level['fixed_markup_cost'],
					'percentage_markup' => $level['percentage_markup_cost'],
					'fixed_markup_base_cost' => $level['fixed_markup_base_cost'],
					'percentage_markup_base_cost' => $level['percentage_markup_base_cost'],
				]);

				foreach ($level['partners'] as $partner) {
					PartnerFeeSharing::create([
						'sharing_level_id' => $feeSharing->id,
                        'partner_id' => $partner['partner_id'],
						'sharing' => $partner['sharing'] ?: 0,
                        'fixed_cost' => $partner['fixed_share'] ?: 0,
                        'percentage_cost' => $partner['percentage_share'] ?: 0,
					]);
				}
			}

			DB::commit();

			return response()->json(['result' => 'success', 'message' => 'Fee updated successfully.']);
		} catch (\Throwable $th) {
			DB::rollback();

			return response()->json(['result' => 'error', 'message' => 'Failed to update fee information.'], 500);
		}
	}

    public function duplicate(Request $request)
    {
        $id = $request->id;
        $fee = FeeInfo::with('feeSharing', 'feeSharing.partnerFeeSharing', 'feeSharing.partnerFeeSharing.partners')->find($id);
        DB::beginTransaction();
        try {
            $newFeeData = $fee->toArray();  // Convert the original fee to an array
            unset($newFeeData['id']);  // Remove the original ID to let the database create a new one
            if($newFeeData['payer'] !== FeeInfo::SPLIT) {
                $newFeeData['sender_pay'] = 0;
                $newFeeData['receiver_pay'] = 0;
            }
            // Optionally, modify some fields for the duplicate (e.g., reset dates or IDs)
            $newFee = FeeInfo::create($newFeeData);  // Create the new FeeInfo record

            // Duplicate the FeeSharing data
            foreach ($fee->feeSharing as $level) {
                $newFeeSharing = FeeSharing::create([
                    'fee_id' => $newFee->id,
                    'sharing_level' => $level->sharing_level,
                    'fixed_base_cost' => $level->fixed_base_cost,
                    'percentage_base_cost' => $level->percentage_base_cost,
                    'fixed_markup' => $level->fixed_markup,
                    'percentage_markup' => $level->percentage_markup,
                    'fixed_markup_base_cost' => $level->fixed_markup_base_cost,
                    'percentage_markup_base_cost' => $level->percentage_markup_base_cost,
                ]);

                // Duplicate the PartnerFeeSharing data
                foreach ($level->partnerFeeSharing as $partner) {
                    PartnerFeeSharing::create([
                        'sharing_level_id' => $newFeeSharing->id,
                        'partner_id' => $partner->partner_id,
                        'sharing' => $partner->sharing,
                        'fixed_cost' => $partner->fixed_cost,
                        'percentage_cost' => $partner->percentage_cost,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['result' => 'success', 'message' => 'Fee created successfully.']);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return response()->json(['result' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function feeDetail(Request $request)
    {
        try {
            $payload = $request->all();
            if(isset($payload['success']) && ($payload['success'] !== 'true')){
                return response()->json(['success' => false, 'data' => "Invalid payload"], 400);
            }
            $data = $payload['data'];
            // Collect all names and merchant keys to minimize queries
            $names = [];
            $merchantKeys = [];

            foreach ($data as $d) {
                $names = array_merge($names, array_keys($d));
                $merchantKeys[] = $d['merchant_key'];
            }

            // Remove duplicates for efficiency
            $names = array_unique($names);
            $merchantKeys = array_unique($merchantKeys);
            $fees = FeeInfo::whereIn('name', $names)
                        ->whereHas('merchant', function($query) use($merchantKeys) {
                            $query->where("key", $merchantKeys);
                        })->get();
            if(!$fees){
                return response()->json(['success' => false, 'data' => "Fees not found"], 404);
            }
            $fees->each(function ($fee) use ($data) {
                $fee->payload = $data;
            });
            $response = new FeeDetailCollection($fees);
            return response()->json(['success' => true, 'data' => $response], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'data' => $th->getMessage()], 500);
        }
    }
}


