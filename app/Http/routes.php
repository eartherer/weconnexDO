<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\User;
use Illuminate\Support\Facades\DB;
Route::get('/news/get', function(){
    echo 'testets';
    return redirect()->action('App\Http\Controllers\NewsController@show');
//    return 'showNews : '.$count;
});
/*
 * Authentication
 */

Route::post('userlogin','UserController@login');//@param user,password @return JWT

/*
 * Profile route
 */
Route::get('/profile/{id}','ProfileController@showbyid')->where('id', '[0-9]+');
/*
 * Area Route
 */
Route::get('/areas/location/{cla}/{clo}/{radius}','AreaController@showbyLocation')->where('id', '[0-9]+');
Route::get('/areas/owner/{id}','AreaController@showbyownerid')->where('id', '[0-9]+');
Route::get('/areas/{id}','AreaController@showbyid')->where('id', '[0-9]+');

/*
 * NEWS route
 */
Route::get('/news/{id}','NewsController@showbyid')->where('id', '[0-9]+');
Route::get('/news/show/','NewsController@show');
Route::get('/news/show/{count}','NewsController@show')->where('count', '[0-9]+');
Route::post('/news/create/','NewsController@create');
Route::delete('/news/delete/{id}','NewsController@destroy');
//Route::get('/news/show/{count?}', 'NewsController@show');

/*
 * Alert route
 */
Route::get('/alerts/{id}','AlertController@showbyid')->where('id', '[0-9]+');
Route::get('/alerts/show/','AlertController@show');
Route::get('/alerts/show/{count}','AlertController@show')->where('count', '[0-9]+');
Route::post('/alerts/create/','AlertController@create');
Route::delete('/alerts/delete/{id}','AlertController@destroy');
/*
 * Topic route
 */
Route::get('/topics/show/{count}','TopicController@show')->where('count', '[0-9]+');
Route::get('/topics/owner/{id}','TopicController@showbyownerid')->where('id', '[0-9]+');
Route::get('/topics/{id}','TopicController@showbyid')->where('id', '[0-9]+');
Route::post('/topics/create','TopicController@create');
Route::delete('/topics/delete/{id}','TopicController@destroy');

/*
 * Reply route
 */
//Route::get('/reply/show/{count}','ReplyController@show')->where('count', '[0-9]+');
Route::get('/reply/create/{id}','ReplyController@showbyownerid')->where('id', '[0-9]+');
Route::get('/reply/topicid/{id}','ReplyController@showbytopicid')->where('id', '[0-9]+');
Route::get('/reply/{id}','ReplyController@showbyid')->where('id', '[0-9]+');
Route::post('/reply/create','ReplyController@create');
Route::delete('/reply/delete/{id}','ReplyController@destroy'); // Pending

/*
 * Chat Route
 */
Route::get('/thread/{id}','ChatController@threadID')->where('id', '[0-9]+');
Route::get('/chat/{threadid}','ChatController@getChat')->where('threadid', '[0-9]+');
Route::post('/chat/create','ChatController@create');

/*
 * File Route
 */
Route::get('/upload', function(){
    return view('upload');
});
Route::post('/profile/upload','FileController@profileupload');
Route::post('/areas/upload','FileController@areaupload');

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('validtoken', function(){

        return DecodeTokenJWT(JWTAuth::getToken());
//    dd(JWTAuth::decode(JWTAuth::getToken()));
    //dd(JWTAuth::getPayload(JWTAuth::getToken())->toArray());
//    $toekn = JWTAuth::parseToken();
//    dd(JWTAuth::parseToken());
//    return $toekn;

    try {
        $tokenDecode = JWTAuth::getPayload(JWTAuth::getToken());
        dd($tokenDecode);
//        if (! $user = JWTAuth::decode(JWTAuth::getToken())) {
//            return response()->json(['user_not_found'], 404);
//        }
        if (! $tokenDecode = JWTAuth::getPayload(JWTAuth::getToken())) {

            return response()->json(['user_not_found'], 404);
        }
    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

        return response()->json(['token_expired'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

        return response()->json(['token_invalid'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

        return response()->json(['token_absent'], $e->getStatusCode());

    }

    return response()->json(compact('user'));
});

Route::get('test', function(){
//    for($i=0;$i<100;$i++) {
//        DB::table('testth')->insert([
//            'title' => 'ภาษาไทยใช้ได้แล้ว',
//            'body' => 'หลังจากการทดสอบภาษาไทยพบกว่า Mysql สามารถใช้ภาษาไทยได้โดยเก็บแบบ utf-8'
//        ]);
//    }
    $result = DB::table('testth')->get();
    //dd($result);
    return $result;
});

Route::get('token', function() {
    //dd(User::first());
//    $user = User::first();
//    $customData = [
//        'username' => $user->name,
//        'userid'    => $user->id
//    ];
//    //dd($customData);
//    $token = JWTAuth::fromUser($user,$customData);
//    $data['token'] = $token;
// return $data;
    $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
//    dd(JWTAuth::encode($customClaims));

    $payload = app('tymon.jwt.payload.factory')->make($customClaims);

    $token = JWTAuth::encode($payload);
//    $jwt = $token->value;
    dd($token);
//    $data222['data'] = $token;
   dd(JWTAuth::decode($token));
    $jsonDecode = JWTAuth::decode($token);
    dd($jsonDecode['username']);
    return $token;
});

function DecodeTokenJWT($token){
    $data['code']       =       '200';
    $data['message']    =       null;
    $data['data']       =       null;
    try {
        $tokenDecode = JWTAuth::getPayload($token);
        $data['data']       =       $tokenDecode->toArray();
        $data['message']    =       'token_valid';
    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        $data['code']       =   $e->getStatusCode();
        $data['message']    =       'token_expired';
//        return response()->json(['token_expired'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        $data['code']       =   $e->getStatusCode();
        $data['message']    =       'token_invalid';
//        return response()->json(['token_invalid'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
        $data['code']       =   $e->getStatusCode();
        $data['message']    =       'token_absent';
//        return response()->json(['token_absent'], $e->getStatusCode());

    }
    return $data;
//    return response()->json(compact('user'));
}