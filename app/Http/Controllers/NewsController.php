<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
USE Validator;

class NewsController extends Controller {

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

        $v = Validator::make($request->all(), [
            'token' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'title' => 'required',
            'body' => 'required',
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
            $result = DB::table('news')->insert([
                'title' => $title,
                'body' => $body,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'adder_id' => $tokenDecoded['data']['numberid'],
            ]);
            return response()->json(['News have been created'], 201);
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

    public function showbyid($id)
    {
        $result = DB::table('news')->where('id','=',$id)->get();
        return $result;
    }
	public function show($count = 10)
	{
        $result = DB::table('news')
            ->join('tmpProfile','news.adder_id','=','tmpProfile.numberid')->take($count)->orderBy('added_date', 'DESC')->get();
        foreach($result as $item){
            $item->url = $_SERVER['SERVER_ADDR'].DIRECTORY_SEPARATOR.$item->url;
        }
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
	public function update($id,Request $request)
	{
        $token  =   $request->token;
        $title  =   $request->title;
        $body  =   $request->body;
        $latitude   =   $request->latitude;
        $longitude  =   $request->longitude;

        $v = Validator::make($request->all(), [
            'token' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'title' => 'required',
            'body' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }
        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            /*
             * Valid Token Find News
             */
            $result = DB::table('news')->where('id','=',$id)->get();
            if($result!=null){
                /*
                * Check Permission
                */
                //$result = DB::table('news')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
                if($result[0]->adder_id == $tokenDecoded['data']['numberid']){
                    /*
                     * Permission Accept
                     */
                    $result = DB::table('news')->where('id','=',$id)->update([
                        'title' => $title,
                        'body' => $body,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ]);
                    return response()->json(['News have been updated'], 200);
                }else{
                    /*
                     * User not have permission
                     */
                    return response()->json(['Permission denied'], 403);
                }
            }else {
                /*
                 * Not found item
                 */
                return response()->json(['News not found'], 404);
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }

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
            $result = DB::table('news')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
            if($result!=null){
                /*
                 * Delete News
                 */
                $result = DB::table('news')->where('id','=',$id)->delete();
                return response()->json(['News have been deleted'], 200);
            }else{
                /*
                 * User not have permission or News not found
                 */
                return response()->json(['News not found'], 404);
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }

	}

}
