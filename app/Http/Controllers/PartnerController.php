<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Partner;
use Validator;

class PartnerController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.partner.create');
    }

    public function store(Request $request)
    {
        @ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$validator = Validator::make($request->all(), [
			'first_name' => 'required|max:20',
			'last_name' => 'required|max:20',
			'email' => 'required|email|unique:partners|max:191',
			'mobile' => 'required|unique:partners|digits:10',
			'company_name' => 'required|max:50',
			'city' => 'nullable|max:20',
			'state' => 'nullable|max:20',
			'zip_code' => 'nullable|max:20',
		]);

        if ($validator->fails()) {
            return redirect('admin/partners/create')
                ->withErrors($validator)
                ->withInput();
		}

        try {
            Partner::create($request->all());

            return redirect('admin/partners/create')->with('success', _lang('Saved Sucessfully'));
        } catch (\Throwable $th) {
            return redirect('admin/partners/create')
                ->withErrors($validator)
                ->withInput();
        }
    }

    public function index()
    {
        $title = _lang('Partner List');

        $partners = Partner::all();

        return view('backend.partner.list',compact('partners', 'title'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $partner = Partner::where('id',$id)->first();

		return view('backend.partner.modal.view',compact('partner','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $partner = Partner::where('id',$id)->first();

		return view('backend.partner.edit',compact('partner','id'));
    }

    public function update(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|max:20',
			'last_name' => 'required|max:20',
            'email' => [
                'required','email',
                Rule::unique('partners')->ignore($id),
            ],
			'mobile' => [
                'required',
                Rule::unique('partners')->ignore($id),
            ],
			'company_name' => 'required|max:50',
			'city' => 'nullable|max:20',
			'state' => 'nullable|max:20',
			'zip_code' => 'nullable|max:20',
		]);

        if ($validator->fails()) {
            return redirect('admin/partners/create')
                ->withErrors($validator)
                ->withInput();
		}

        $partner = Partner::where('id',$id)->first();

        try {
            $partner->first_name = $request->input('first_name');
            $partner->last_name = $request->input('last_name');
            $partner->email = $request->input('email');
            $partner->mobile = $request->input('mobile');
            $partner->company_name = $request->input('company_name');
            $partner->city = $request->input('city');
            $partner->state = $request->input('state');
            $partner->zip_code = $request->input('zip_code');
            $partner->save();

            return redirect('admin/partners')->with('success', _lang('Saved Sucessfully'));
        } catch (\Throwable $th) {
            return redirect('admin/partners')
                ->withErrors($validator)
                ->withInput();
        }
    }

    public function getPartners(Request $request){
        try {
            $ids = $request->ids;
            $partners = Partner::whereIn("id", $ids)->latest()->get();
            return response()->json([
                "success" => true,
                "data" => $partners
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "data" => $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $partner = Partner::where('id', $id)->first();

        if($partner) {
            $partner->delete();
            return redirect('admin/partners')->with('success',_lang('Removed Sucessfully'));
        } else {
            return redirect('admin/partners')->withErrors(_lang('Something went wrong!'));
        }
    }
}
