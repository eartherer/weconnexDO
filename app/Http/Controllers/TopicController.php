<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class TopicController extends Controller {


    public function show($count = 10)
    {
        $result = DB::table('we_topics')->take($count)->orderBy('postdate', 'DESC')->get();
        return $result;
    }
    public function showbyid($id){
        $Result     =   DB::table('we_topics')
            ->where('id','=',$id)->get();
        if($Result == null){
            abort(404);
        }
        return $Result;
    }


    public function showbyownerid($id){
        $Result     =   DB::table('we_topics')
            ->where('postid','=',$id)->get();
        if($Result == null){
            abort(404);
        }
        return $Result;
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

        $v = Validator::make($request->all(), [
            'token' => 'required',
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
            $result = DB::table('we_topics')->insert([
                'title' => $title,
                'body' => $body,
                'postid' => $tokenDecoded['data']['numberid'],
                'lastAnswerid' => $tokenDecoded['data']['numberid'],
                'lastAnswerDate' => time(),
            ]);
            return response()->json(['Topic have been created'], 201);
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
//	public function show($id)
//	{
//		//
//	}

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

        $v = Validator::make($request->all(), [
            'token' => 'required',
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
            $result = DB::table('we_topics')->where('id','=',$id)->get();
            if($result!=null){
                /*
                * Check Permission
                */
                //$result = DB::table('news')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
                if($result[0]->postid == $tokenDecoded['data']['numberid']){
                    /*
                     * Permission Accept
                     */
                    $result = DB::table('we_topics')->where('id','=',$id)->update([
                        'title' => $title,
                        'body' => $body,
                    ]);
                    return response()->json(['Topic have been updated'], 200);
                }else{
                    /*
                     * User not have permission
                     */
                    return response()->json(['Permission denied'], 403);
                }
            }else {
                /*
                 * Not found Topic
                 */
                return response()->json(['Topic not found'], 404);
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
            $result = DB::table('we_topics')->where('id','=',$id)->where('postid','=',$tokenDecoded['data']['numberid'])->get();
            if($result!=null){
                /*
                 * Delete Topic
                 */
                $result = DB::table('we_topics')->where('id','=',$id)->delete();
                return response()->json(['Topic have been deleted'], 200);
            }else{
                /*
                 * User not have permission or Topic not found
                 */
                return response()->json(['Topic not found'], 404);
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }

    }

}
