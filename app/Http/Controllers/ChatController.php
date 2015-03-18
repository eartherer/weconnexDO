<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;
class ChatController extends Controller {

    public function getChat($threadid = null){
        $result = DB::table('Chat_Message')->where('threadid','=',$threadid)->get();
        return $result;
    }

    public function threadID($id = null){
//        dd($id);
        $token = Input::get('token');
        $v = Validator::make(['token' => $token,'id' => $id], [
            'token' => 'required',
            'id' => 'required|numeric',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }

        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200) {
            /*
             * Get Caller userID
             */
            $callerID = $tokenDecoded['data']['numberid'];
            $response =  $this->get_thread_id_by_pair_id($callerID,$id);
            return $response;
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }
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
	public function create()
	{
		//
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
	public function show($id)
	{
		//
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
	public function destroy($id)
	{
		//
	}
    function get_thread_id_by_pair_id($user_id_1,$user_id_2){

        do{
            $result = DB::select('SELECT id AS threadID, userid AS USER_1, (SELECT DISTINCT userid FROM Thread_Participant WHERE userid =?) AS USER_2
            FROM Thread_Participant WHERE userid =? AND id IN (SELECT id FROM Thread_Participant WHERE userid =?)',[
                $user_id_2,$user_id_1,$user_id_2
            ]);
            if($result == null){
                /*
                 * Get max thread ID
                 */
                $max_thread_id = DB::table('Thread_Participant')->max('id');
                 /*
                  * Create Thread
                  */
                $result = DB::table('Thread_Participant')->insert([
                    'id'        =>  $max_thread_id + 1,
                    'userid'    =>  $user_id_1,
                ]);
                $result = DB::table('Thread_Participant')->insert([
                    'id'        =>  $max_thread_id + 1,
                    'userid'    =>  $user_id_2,
                ]);
                /*
                 * Goto Query Again
                 */
            }else{
                /*
                 * return Thread Detail
                 */
                return $result;
            }
        }while(1);
    }

}
