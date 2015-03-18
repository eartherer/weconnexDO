<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class AlertController extends Controller {


    public function testlog(Request $request){
        dd($request);
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */

    public function create(Request $request)
    {
        $token  =   $request->token;
        $title  =   $request->title;
        $body  =   $request->body;
        $latitude   =   $request->latitude;
        $longitude  =   $request->longitude;
        $group  =   $request->group;

        $v = Validator::make($request->all(), [
            'token' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'title' => 'required',
            'body' => 'required',
            'group' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }

        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            /*
             * Valid Token Add Item
             */
            $result = DB::table('alerts')->insert([
                'title' => $title,
                'body' => $body,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'adder_id' => $tokenDecoded['data']['numberid'],
                'group' => $group,
            ]);
            return response()->json(['Alert have been created'], 201);
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }

    }
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function show($count = 10)
    {
        $result = DB::table('alerts')->take($count)->orderBy('added_date', 'DESC')->get();
        return $result;
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function destroy($id = 0,Request $request)
    {
        $token  =   $request->token;
        $v = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }
        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            /*
             * Valid Token Delete Item
             */

            /*
             * Check Permission
             */
            $result = DB::table('alerts')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
            if($result!=null){
                /*
                 * Delete News
                 */
                $result = DB::table('alerts')->where('id','=',$id)->delete();
                return response()->json(['alerts have been deleted'], 200);
            }else{
                /*
                 * User not have permission or News not found
                 */
                return response()->json(['alerts not found'], 404);
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }

    }

    public function showbyid($id)
    {
        $result = DB::table('alerts')->where('id','=',$id)->get();
        return $result;
    }
}
