<?php

namespace App\Http\Controllers;

use App\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionCategoryController extends Controller
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
        $title = _lang('Transaction Category List');

        $transCategory = TransactionCategory::all();

        return view('backend.transaction-category.list',compact('transCategory', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.transaction-category.create');
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
			'name' => 'required|max:20'
		]);

        if ($validator->fails()) {
            return redirect('admin/transaction-category/create')
                ->withErrors($validator)
                ->withInput();
		}

        try {
            TransactionCategory::create($request->all());

            return redirect('admin/transaction-category/create')->with('success', _lang('Saved Sucessfully'));
        } catch (\Throwable $th) {
            return redirect('admin/transaction-category/create')
                ->withErrors($validator)
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TransactionCategory  $transactionCategory
     * @return \Illuminate\Http\Response
     */
    public function show(TransactionCategory $transactionCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TransactionCategory  $transactionCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $transCategory = TransactionCategory::where('id',$id)->first();

		return view('backend.transaction-category.edit',compact('transCategory','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransactionCategory  $transactionCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
			'name' => 'required|max:20'
		]);

        if ($validator->fails()) {
            return redirect('admin/transaction-category/create')
                ->withErrors($validator)
                ->withInput();
		}

        $transCategory = TransactionCategory::where('id',$id)->first();

        try {
            $transCategory->name = $request->input('name');
            $transCategory->save();

            return redirect('admin/transaction-category')->with('success', _lang('Saved Sucessfully'));
        } catch (\Throwable $th) {
            return redirect('admin/transaction-category')
                ->withErrors($validator)
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransactionCategory  $transactionCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transCategory = TransactionCategory::where('id', $id)->first();

        if($transCategory) {
            $transCategory->delete();
            return redirect('admin/transaction-category')->with('success',_lang('Removed Sucessfully'));
        } else {
            return redirect('admin/transaction-category')->withErrors(_lang('Something went wrong!'));
        }
    }
}
