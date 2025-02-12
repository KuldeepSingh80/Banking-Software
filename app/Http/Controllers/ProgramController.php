<?php

namespace App\Http\Controllers;

use App\FeesCatelog;
use App\Merchant;
use App\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$title = _lang('Programs');

		$programs = Program::orderBy('id', 'DESC')->get();

		return view('backend.programs.listing', compact('title', 'programs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $feesCatalogs = FeesCatelog::latest()->get();
        $merchants = Merchant::latest()->get();
        $title = _lang('Add New Program');
        return view('backend.programs.create', compact('title', 'feesCatalogs', 'merchants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'merchants' => 'required',
                'fees_catalogs' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "data" => "Please fill all required fields."
                ], 400);
            }

            $oldProgram = Program::where('name', $request->name)->first();
            if($oldProgram){
                return response()->json([
                    "success" => false,
                    "data" => "Program already exists!"
                ], 400);
            }

            $program = new Program();
            $program->name = $request->name;
            $program->save();

            $program->merchants()->sync($request->merchants);
            $program->feesCatalogs()->sync($request->fees_catalogs);

            DB::commit();

            return response()->json([
                "success" => true,
                "data" => "Program saved successfully!"
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "success" => false,
                "data" => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $program = Program::where('id',$id)->with(['merchants', 'feesCatalogs'])->first();
		return view('backend.programs.modal.view',compact('program','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        $title = _lang('Edit Program');

        $program = Program::where('id',$id)->with(['merchants', 'feesCatalogs'])->first();
        $feesCatalogs = FeesCatelog::latest()->get();
        $merchants = Merchant::latest()->get();
        if($program) {
            return view('backend.programs.edit',compact('program','id', 'title', 'feesCatalogs', 'merchants'));
        }

        return redirect()->back()->with('error', _lang('Program  doesn\'t exist!'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'merchants' => 'required',
                'fees_catalogs' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "data" => "Please fill all required fields."
                ], 400);
            }

            $oldProgram = Program::where('name', $request->name)->where('id', '!=', $id)->first();
            if($oldProgram){
                return response()->json([
                    "success" => false,
                    "data" => "Program already exists!"
                ], 400);
            }

            $program = Program::find($id);
            $program->name = $request->name;
            $program->save();

            $program->merchants()->sync($request->merchants);
            $program->feesCatalogs()->sync($request->fees_catalogs);

            DB::commit();

            return response()->json([
                "success" => true,
                "data" => "Program saved successfully!"
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "success" => false,
                "data" => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $program = Program::where('id', $id)->first();

        if($program) {
            $program->delete();
            return redirect('admin/programs')->with('success',_lang('Removed Sucessfully'));
        } else {
            return redirect('admin/programs')->withErrors(_lang('Something went wrong!'));
        }
    }
}
