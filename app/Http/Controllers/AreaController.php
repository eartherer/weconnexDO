<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller {


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
        return $Result;
    }


    public function showbyownerid($id){
        $Result     =   DB::table('we_areas')
            ->where('owner_id','=',$id)->get();
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
