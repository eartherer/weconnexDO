<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class UserController extends Controller {

    public function login(Request $request){
        $username = $request->user;
        $password = $request->password;
        $Result     =   DB::table('we_users')
            ->where('username','=',$username)
            ->where('password','=',$password)->first();
        if($Result == null){
            abort(401,'Username or password Incorrect');
        }else{
            /*
             * Make JSON Web Token
             */
            $payloadarray = [
                'username' => $username,
                'numberid' => $Result->id,
            ];
            $payload = app('tymon.jwt.payload.factory')->make($payloadarray);
            $token = JWTAuth::encode($payload);
            return $token;
        }
//        dd($Result);
//        abort(401,'Invalid Username');
//        dd($username);
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
        $id          =   $request->id;
        $username    =   $request->username;
        $password    =   $request->password;
        $email       =   $request->email;
        $v = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'username' => 'required|alpha_num',
            'password' => 'required|alpha_num',
            'email' => 'required|email',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }
        /*
         * Store in database
         */
        $result = DB::table('we_users')->insert([
            'id'            =>  $id,
            'username'      =>  $username,
            'password'      =>  $password,
            'email'         =>  $email,
        ]);
        return response()->json(['User have been created'], 201);
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

    function DecodeTokenJWT($token)
    {
        $data['code'] = '200';
        $data['message'] = null;
        $data['data'] = null;
        try {
            $tokenDecode = JWTAuth::getPayload($token);
            $data['data'] = $tokenDecode->toArray();
            $data['message'] = 'token_valid';
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $data['code'] = $e->getStatusCode();
            $data['message'] = 'token_expired';
//        return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $data['code'] = $e->getStatusCode();
            $data['message'] = 'token_invalid';
//        return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            $data['code'] = $e->getStatusCode();
            $data['message'] = 'token_absent';
//        return response()->json(['token_absent'], $e->getStatusCode());

        }
        return $data;
    }
}
