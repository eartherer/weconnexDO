<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class AreaController extends Controller {

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


    public function getAreaPicListByAreaID($id){
        $Result     =   DB::table('pic_area')
            ->where('areaid','=',$id)->get();
        return $Result;
    }
}
