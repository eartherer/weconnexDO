<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Validator;
//use Illuminate\Http\Request;

class FileController extends Controller {
    public function areaupload(Request $request){
//        dd($request);
        $token  =   $request->token;
        $areaid  =   $request->areaid;
        $v = Validator::make($request->all(), [
            'token' => 'required',
            'areaid'=> 'required|numeric',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }
        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            $file = array('image' => Input::file('file'));
            $message = [
                'image.required'=>'ไม่พบไฟล์รูปภาพ',
                'image.max'=>'ขนาดภาพเกิน 700 kb',
                'image.mimes'=>'อนุญาติให้ใช้นามสกุล .jpg .jpeg หรือ .png เท่านั้น',
            ];
            $validator = Validator::make($file,['image'=>'required|max:700|mimes:jpg,png,jpeg'],$message);
            if ($validator->fails()) {
                // send back to the page with the input data and errors
                return response()->json($validator->errors(), 404);
            }
            else {
                // checking file is valid.
                if (Input::file('file')->isValid()) {
                    $destinationPath = 'uploads/area_pic'; // upload path
                    $extension = Input::file('file')->getClientOriginalExtension(); // getting image extension
                    $fileName = md5(Input::file('file')->getClientOriginalName()).'_'.time()."_".$extension; // renameing image
                    Input::file('file')->move($destinationPath, $fileName); // uploading file to given path
                    // sending back with message
                    $finalfilepath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;
                    /*
                     * Delete Area pic url to database
                     */
//                    $res  = DB::table('pic_area')->where('uploader','=',$tokenDecoded['data']['numberid'])->delete();
                    /*
                     * Insert Area pic url to database
                     */
                    $res  = DB::table('pic_area')->insert([
                        'uploader' =>  $tokenDecoded['data']['numberid'],
                        'url'   => $finalfilepath,
                        'areaid' => $areaid,
                    ]);
                    return response()->json(['Upload successfully',$finalfilepath], 200);
                    //return Redirect::to('upload');
                }
                else {
                    // sending back with error message.
                    return response()->json(['uploaded file is not valid'], 404);
                    //return Redirect::to('upload');
                }
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }
    }


    public function profileupload(Request $request){
//        dd($request);
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
            $file = array('image' => Input::file('file'));
            $message = [
                'image.required'=>'ไม่พบไฟล์รูปภาพ',
                'image.max'=>'ขนาดภาพเกิน 700 kb',
                'image.mimes'=>'อนุญาติให้ใช้นามสกุล .jpg .jpeg หรือ .png เท่านั้น',
            ];
            $validator = Validator::make($file,['image'=>'required|max:700|mimes:jpg,png,jpeg'],$message);
            if ($validator->fails()) {
                // send back to the page with the input data and errors
                return response()->json($validator->errors(), 404);
            }
            else {
                // checking file is valid.
                if (Input::file('file')->isValid()) {
                    $destinationPath = 'uploads/user_profile_pic'; // upload path
                    $extension = Input::file('file')->getClientOriginalExtension(); // getting image extension
                    $fileName = $tokenDecoded['data']['numberid'].'.'.$extension; // renameing image
                    Input::file('file')->move($destinationPath, $fileName); // uploading file to given path
                    // sending back with message
                    $finalfilepath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;
                    /*
                     * Delete Profile pic url to database
                     */
                    $res  = DB::table('pic_profile')->where('numberid','=',$tokenDecoded['data']['numberid'])->delete();
                    /*
                     * Insert Profile pic url to database
                     */
                    $res  = DB::table('pic_profile')->insert([
                        'numberid' =>  $tokenDecoded['data']['numberid'],
                        'url'   => $finalfilepath,
                    ]);
                    return response()->json(['Upload successfully',$finalfilepath], 200);
                    //return Redirect::to('upload');
                }
                else {
                    // sending back with error message.
                    return response()->json(['uploaded file is not valid'], 404);
                    //return Redirect::to('upload');
                }
            }
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

}
