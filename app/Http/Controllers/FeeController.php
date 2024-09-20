<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\FeeInfo;
use App\FeeSharing;
use App\PartnerFeeSharing;
use App\Http\Requests\SaveFeeRequest;

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

        return view('backend.administration.fee_management.create', compact('title'));
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

        $fee = FeeInfo::where('id',$id)->with('feeSharing', 'feeSharing.partnerFeeSharing')->first();

        if($fee) {
            return view('backend.administration.fee_management.edit',compact('fee','id', 'title'));
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
                        'partner' => $partner['partner_index'],
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
            return response()->json(['result' => 'error', 'message' => 'Failed to save fee information.'], 500);
        }
	}

	public function update_fee(SaveFeeRequest $request, $id)
	{
		$fee = FeeInfo::where('id', $id)->first();

		if (!$fee) {
			return response()->json(['result' => 'error', 'message' => 'Fee doesn\'t exist!'], 404);
		}

		$data = $request->validated();

		DB::beginTransaction();

		try {
			$feeSharingRecords = FeeSharing::where('fee_id', $id)->get();

			foreach ($feeSharingRecords as $feeSharing) {
				PartnerFeeSharing::where('sharing_level_id', $feeSharing->id)->delete();
			}

			FeeSharing::where('fee_id', $id)->delete();

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
						'partner' => $partner['partner_index'],
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
			dd($th);
			return response()->json(['result' => 'error', 'message' => 'Failed to update fee information.'], 500);
		}
	}
}
