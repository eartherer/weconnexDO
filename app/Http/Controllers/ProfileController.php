<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class ProfileController extends Controller {


    public function showbyid($id){
        $Result     =   DB::table('we_profiles')
            ->join('pic_profile','we_profiles.id','=','pic_profile.numberid')
            ->where('id','=',$id)->get();
        if($Result == null){
            abort(404);
        }
//        dd($Result[0]->url);
        $Result[0]->url = $_SERVER['SERVER_ADDR'].DIRECTORY_SEPARATOR.$Result[0]->url;
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
        $token          =   $request->token;
        $name           =   $request->name;
        $surname        =   $request->surname;
        $sex            =   $request->sex;
        $birthday       =   $request->birthday;
        $house_no       =   $request->house_no;
        $village_no     =   $request->village_no;
        $alley          =   $request->alley;
        $road           =   $request->road;
        $sub_district   =   $request->sub_district;
        $district       =   $request->district;
        $province       =   $request->province;
        $zip_code       =   $request->zip_code;
        $phone_number   =   $request->phone_number;
        $mobilephone_number  =   $request->mobilephone_number;

        $v = Validator::make($request->all(), [
            'token' => 'required',
            'name' => 'required',
            'surname' => 'required',
            'sex' => 'required',
            'birthday' => 'required',
            'house_no' => 'required',
            'village_no' => 'required',
            'alley' => 'required',
            'road' => 'required',
            'sub_district' => 'required',
            'district' => 'required',
            'province' => 'required',
            'zip_code' => 'required',
            'phone_number' => 'required',
            'mobilephone_number' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }
        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            $result = DB::table('we_profiles')->insert([
                'id' => $tokenDecoded['data']['numberid'],
                'name' => $name,
                'surname' => $surname,
                'sex' => $sex,
                'birthday' => $birthday,
                'house_no' => $house_no,
                'village_no' => $village_no,
                'alley' => $alley,
                'road' => $road,
                'sub_district' => $sub_district,
                'district' => $district,
                'province' => $province,
                'zip_code' => $zip_code,
                'phone_number' => $phone_number,
                'mobilephone_number' => $mobilephone_number,
            ]);
            return response()->json(['Profile have been created'], 201);
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

}
