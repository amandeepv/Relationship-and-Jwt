<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use JWTAuthException;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    //Index view Facebook
    public function index()
    {
        $users = User::orderBy('id','asc')->get();
        return $users;
    } 
    //User Register on Facebook
    public function register(Request $request)
    {
        $this->validateData($request->user, User::validationRules());
		return User::store($request->user);
	}
	//update User data
	public function update(Request $request, $id)
    {
        $this->validateData($request->user, User::validationRules($id));
		$user = User::findOrFail($id);
		$user->updateUser($request->user);
		return $user;
    }
    //User login on Facebook
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = null;
        //Check user status
        $user = User::whereEmail($request["email"])->first();
            if(sizeof($user->all()) > 0){
            $status = $user["status"];
                if($status!= 'active'){
                    $error = array('required' => 'This account is not active.');
                    throw new StoreResourceFailedException('Could not create record.', $error);
                }
            }
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['invalid_email_or_password'], 422);
                }
            } catch (JWTAuthException $e) {
                return response()->json(['failed_to_create_token'], 500);
            }
            return response()->json(compact('token'));
    }
    //User verification before login
    public function userVerfication($token)
    {
        $verifyUser = User::where('token', $token)->first();
        //Token check
        {
            $error = array('required' => 'Token does not exist.');
            if($verifyUser === null) {
                throw new StoreResourceFailedException('Could not create record.', $error);
            }   
        }
        {
            $error = array('required' => 'token already active.');
            if($verifyUser->status === 'active'){
                throw new StoreResourceFailedException('Could not create record.', $error);
            }
        }
        $verifyUser->statusUpdate($verifyUser);
        return $verifyUser;
    }
    //User logout on facebook
    public function logout(Request $request)
    {
        return User::logout($request);
    }
    //user get detail with use of tokken
    public function getUser(Request $request)
    {
        return User::getUser($request);
    }
    public function getTokken($token)
    {
      $getTokken = User::where('token', $token)->first();
      return $getTokken->token;
    }
}
