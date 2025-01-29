<?php

namespace App\Http\Controllers;

use App\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{

    
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
        $title = _lang('Merchants List');

        $merchants = Merchant::all();

        return view('backend.merchant.list',compact('merchants', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.merchant.create');
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
			'name' => 'required|max:20',
			'key' => 'required',
		]);

        if ($validator->fails()) {
            return redirect('admin/merchants/create')
                ->withErrors($validator)
                ->withInput();
		}

        try {
            Merchant::create($request->all());

            return redirect('admin/merchants/create')->with('success', _lang('Saved Sucessfully'));
        } catch (\Throwable $th) {
            return redirect('admin/merchants/create')
                ->withErrors($validator)
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(Merchant $merchant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $merchant = Merchant::where('id',$id)->first();

		return view('backend.merchant.edit',compact('merchant','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:20',
			'key' => 'required',
		]);

        if ($validator->fails()) {
            return redirect('admin/merchants/create')
                ->withErrors($validator)
                ->withInput();
		}

        $merchant = Merchant::where('id',$id)->first();

        try {
            $merchant->name = $request->input('name');
            $merchant->key = $request->input('key');
            $merchant->save();

            return redirect('admin/merchants')->with('success', _lang('Saved Sucessfully'));
        } catch (\Throwable $th) {
            return redirect('admin/merchants')
                ->withErrors($validator)
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merchant = Merchant::where('id', $id)->first();

        if($merchant) {
            $merchant->delete();
            return redirect('admin/merchants')->with('success',_lang('Removed Sucessfully'));
        } else {
            return redirect('admin/merchants')->withErrors(_lang('Something went wrong!'));
        }
    }
}
