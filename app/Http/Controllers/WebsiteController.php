<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Website;
use JWTAuthException;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Tymon\JWTAuth\Exceptions\JWTException;

class WebsiteController extends Controller
{
    //index view
    public function index()
    {
        $website = Website::orderBy('id','asc')->get()->load('user');
        return $website;
    }
    
    public function store(Request $request)
    {
        $this->validateData($request->website, Website::validationRules());
		return Website::store($request->website);
	}
	
	public function show($id)
    {
		$website = Website::findOrFail($id);
		return $website->load('user');
    }
    
    public function update(Request $request, $id)
    {
        $this->validateData($request->website, Website::validationRules());
		$website = Website::findOrFail($id);
		$website->updateWebsite($request->website);
		return $website;
    }
    
    public function destroy($id)
	{
    	$website = Website::findOrFail($id);
 
    	if (!$website) {
        	return response()->json([
            'success' => false,
            'message' => 'Sorry, account with id ' . $id . ' cannot be found'
        ], 400);
    }
 
    if ($website->delete()) {
        return response()->json([
            'success' => true
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'account could not be deleted'
        ], 500);
    }
	}
}
