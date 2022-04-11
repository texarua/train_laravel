<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\testApi;
use Validator;
class TestApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $product =  testApi::all();
         return response()->json([
            'status' => 200,
            'product' => $product
        ]);
    }

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
        //
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'integer',
            'availability' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
           // return response()->json($validator->messages(), 200);
          return response()->json([
                "message" => "Validation Failed",
                'status' => 422,
                'errors'=>$validator->errors()]);
        }

        $product = testApi::create($request->all());
        return response()->json([
                'message' => 'Great success! New task created',
                'status' => 200,
                'product' => $product
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!empty($id)) {

            $product = testApi::find($id);
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        }
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
            $testApi = testApi::findOrFail($id);

            //
            $rules = [
                'title' => 'required|max:255',
                'description' => 'required',
                'price' => 'integer',
                'availability' => 'boolean'
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
               // return response()->json($validator->messages(), 200);
              return response()->json([
                    "message" => "Validation Failed",
                    'status' => 422,
                    'errors'=>$validator->errors()]);
            }
        
            $data = $request->all();
            if ($testApi->update($data)) {
                return response()->json([
                    'message' => 'id = ' .$id. 'is update',
                    'status' => 200,
                    'product' => $data
                ]);
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
        testApi::find($id)->delete();
        return response()->json([
                    'message' => 'id = ' .$id. 'is delete',
                    'status' => 204
                ]);
    }
}
