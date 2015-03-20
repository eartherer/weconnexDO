<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class ReplyController extends Controller {


    public function showbytopicid($id){
        $Result     =   DB::table('we_replies')
            ->where('topicid','=',$id)->get();
        if($Result == null){
            abort(404);
        }
        return $Result;
    }

    public function showbyid($id){
        $Result     =   DB::table('we_replies')
            ->where('id','=',$id)->get();
        if($Result == null){
            abort(404);
        }
        return $Result;
    }


    public function showbyownerid($id){
        $Result     =   DB::table('we_replies')
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
        $topicid  =   $request->topicid;
        $body  =  $request->body;

        $v = Validator::make($request->all(), [
            'token' => 'required',
            'topicid' => 'required',
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

            /*
             * Add reply to Database
             */
            $result = DB::table('we_replies')->insert([
                'topicid' => $topicid,
                'body' => $body,
                'postid' => $tokenDecoded['data']['numberid'],
                'postdate' => date('Y-m-d H:i:s',time()),
            ]);

            /*
             * Update Topic
             */

            DB::statement('UPDATE we_topics SET replies=replies+1,views=views+1 where id = ?',[$topicid]);
            $result = DB::table('we_topics')->where('id','=',$topicid)->update([
                'lastAnswerid' => $tokenDecoded['data']['numberid'],
                'lastAnswerDate' => date('Y-m-d H:i:s',time()),
            ]);
//            DB::table('we_topics')->increment('replies', 1)->where('id','=',$topicid);
//            DB::table('we_topics')->increment('views', 1)->where('id','=',$topicid);
            return response()->json(['reply have been created'], 201);
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
        $body  =  $request->body;

        $v = Validator::make($request->all(), [
            'token' => 'required',
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
            $result = DB::table('we_replies')->where('id','=',$id)->get();
            if($result!=null){
                /*
                * Check Permission
                */
                //$result = DB::table('news')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
                if($result[0]->postid == $tokenDecoded['data']['numberid']){
                    /*
                     * Permission Accept
                     */
                    $result = DB::table('we_replies')->where('id','=',$id)->update([
                        'body' => $body,
                    ]);
                    return response()->json(['Reply have been updated'], 200);
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
                return response()->json(['Reply not found'], 404);
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
	public function destroy($id)
	{
		//
	}

}
