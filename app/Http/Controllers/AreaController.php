<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class AreaController extends Controller {

    public function getDataInContainer($id,$conid){
        $result = DB::table('Bee_Data')->where('containerid','=',$conid)->get();
        if($result == null){
            return response()->json(['Data not found'], 404);
        }else{
            return $result;
        }
    }
    public function getContainer($id){
        $result = DB::table('we_area_DataContainer')->where('areaid','=',$id)->get();
        if($result == null){
            return response()->json(['Data Container not found'], 404);
        }else{
            return $result;
        }

        dd($result);
        return 'Get Contain';
    }

    public function deleteAreaPicture($id = null,Request $request){
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
             * Find Area Pic
             */
            $result = DB::table('pic_area')->where('id','=',$id)->get();
            if($result!=null){
                /*
                 * Check Permission
                 */
                if($result[0]->uploader == $tokenDecoded['data']['numberid']){
                    /*
                     * Permission accept
                     */
                    $flgDelete = unlink($result[0]->url);
                    $result = DB::table('pic_area')->where('id','=',$id)->delete();
                    return response()->json(['Area Picture have been deleted'], 200);
                }else {
                    /*
                     * Permission denied
                     */
                    return response()->json(['Permission denied'], 403);
                }
            }else{
                /*
                 * User not have permission or News not found
                 */
                return response()->json(['Area Picture not found'], 404);
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }
    }

    public function showbyLocation($cla, $clo, $radius){
        $rad_unit = ($radius/110);
        $la_min = $cla - $rad_unit;
        $la_max = $cla + $rad_unit;
        $lo_min = $clo - $rad_unit;
        $lo_max = $clo + $rad_unit;
        $Result     =   DB::table('we_areas')
                        ->whereBetween('latitude',array($la_min,$la_max))
                        ->whereBetween('longitude',array($lo_min,$lo_max))->get();
        return $Result;
    }


    public function showbyid($id){
        $Result     =   DB::table('we_areas')
            ->where('id','=',$id)->get();
        if($Result == null){
            abort(404);
        }
        foreach($Result as $item){
            $item->urlPicList = $this->getAreaPicListByAreaID($item->id);
        }
        return $Result;
    }


    public function showbyownerid($id){
        $Result     =   DB::table('we_areas')
            ->where('owner_id','=',$id)->get();
        if($Result == null){
            abort(404);
        }
        foreach($Result as $item){
            $item->urlPicList = $this->getAreaPicListByAreaID($item->id);
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
        $token          =   $request->token;
        $latitude       =   $request->latitude;
        $longitude      =   $request->longitude;
        $isOwner        =   $request->isOwner;
        $size           =   $request->size;
        $owner_id       =   $request->owner_id;
        $area_type      =   $request->area_type;
        $land_type      =   $request->land_type;
        $house_no       =   $request->house_no;
        $village_no     =   $request->village_no;
        $alley          =   $request->alley;
        $road           =   $request->road;
        $sub_district   =   $request->sub_district;
        $district       =   $request->district;
        $province       =   $request->province;
        $zip_code       =   $request->zip_code;
        $group       =   $request->group;


        $v = Validator::make($request->all(), [
            'token' => 'required',
            'latitude'        => 'required',
            'longitude'       => 'required',
            'isOwner'         => 'required',
            'size'            => 'required',
            'owner_id'        => 'required',
            'area_type'       => 'required',
            'land_type'       => 'required',
            'house_no' => 'required',
            'village_no' => 'required',
            'alley' => 'required',
            'road' => 'required',
            'sub_district' => 'required',
            'district' => 'required',
            'province' => 'required',
            'zip_code' => 'required',
            'group' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }

//        dd($request->all());
        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            $result = DB::table('we_areas')->insert([
                'latitude'       => $latitude,
                'longitude'      => $longitude,
                'isOwner'        => $isOwner,
                'size'           => $size,
                'owner_id'       => $owner_id,
                'area_type'      => $area_type,
                'land_type'      => $land_type,
                'house_no' => $house_no,
                'village_no' => $village_no,
                'alley' => $alley,
                'road' => $road,
                'sub_district' => $sub_district,
                'district' => $district,
                'province' => $province,
                'zip_code' => $zip_code,
                'group'    => $group,
                'adder_id'    => $tokenDecoded['data']['numberid'],
            ]);
            return response()->json(['Area have been created'], 201);
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
    public function update($id,Request $request)
    {
        $token          =   $request->token;
        $latitude       =   $request->latitude;
        $longitude      =   $request->longitude;
        $isOwner        =   $request->isOwner;
        $size           =   $request->size;
        $owner_id       =   $request->owner_id;
        $area_type      =   $request->area_type;
        $land_type      =   $request->land_type;
        $house_no       =   $request->house_no;
        $village_no     =   $request->village_no;
        $alley          =   $request->alley;
        $road           =   $request->road;
        $sub_district   =   $request->sub_district;
        $district       =   $request->district;
        $province       =   $request->province;
        $zip_code       =   $request->zip_code;
        $group       =   $request->group;


        $v = Validator::make($request->all(), [
            'token' => 'required',
            'latitude'        => 'required',
            'longitude'       => 'required',
            'isOwner'         => 'required',
            'size'            => 'required',
            'owner_id'        => 'required',
            'area_type'       => 'required',
            'land_type'       => 'required',
            'house_no' => 'required',
            'village_no' => 'required',
            'alley' => 'required',
            'road' => 'required',
            'sub_district' => 'required',
            'district' => 'required',
            'province' => 'required',
            'zip_code' => 'required',
            'group' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json($v->errors(), 400);
        }
        $tokenDecoded = DecodeTokenJWT($token);
        if($tokenDecoded['code'] == 200){
            /*
             * Valid Token Find alerts
             */
            $result = DB::table('we_areas')->where('id','=',$id)->get();
            if($result!=null){
                /*
                * Check Permission
                */
                //$result = DB::table('news')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
                if($result[0]->adder_id == $tokenDecoded['data']['numberid']){
                    /*
                     * Permission Accept
                     */
                    $result = DB::table('we_areas')->where('id','=',$id)->update([
                        'latitude'       => $latitude,
                        'longitude'      => $longitude,
                        'isOwner'        => $isOwner,
                        'size'           => $size,
                        'owner_id'       => $owner_id,
                        'area_type'      => $area_type,
                        'land_type'      => $land_type,
                        'house_no' => $house_no,
                        'village_no' => $village_no,
                        'alley' => $alley,
                        'road' => $road,
                        'sub_district' => $sub_district,
                        'district' => $district,
                        'province' => $province,
                        'zip_code' => $zip_code,
                        'group'    => $group,
                    ]);
                    return response()->json(['Area have been updated'], 200);
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
                return response()->json(['Area not found'], 404);
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
            $result = DB::table('we_areas')->where('id','=',$id)->where('adder_id','=',$tokenDecoded['data']['numberid'])->get();
            if($result!=null){
                /*
                 * Delete Area
                 */
                $result = DB::table('we_areas')->where('id','=',$id)->delete();
                return response()->json(['Area have been deleted'], 200);
            }else{
                /*
                 * User not have permission or News not found
                 */
                return response()->json(['Area not found'], 404);
            }
        }else{
            /*
             * Invalid Token
             */
            return response()->json([$tokenDecoded['message']], $tokenDecoded['code']);
        }

    }



    public function getAreaPicListByAreaID($id){
        $Result     =   DB::table('pic_area')
            ->where('areaid','=',$id)->get();
        return $Result;
    }
}
