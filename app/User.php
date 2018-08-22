<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use JWTAuth;
use JWTAuthException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Mail;
use App\Mail\UserMail;

use App\Website;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
            'first_name',
            'last_name',
            'email',
            'password',
            'contact_number',
            'date_of_birth',
            'gender',
        ];
    
    protected $hidden = [
        'password',
        'token',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    //Relationships
    public function friends()
    {
        return $this->hasMany(FacebookFriend::class);
    }
    
    public function messages()
    {
        return $this->hasMany(FacebookMessage::class);
    }
    
    public function websites()
    {
        return $this->hasMany(Website::class);
    }
    
    // store user data
    public static function store($request, $user = null)
    {
		if($user === null){
			$user = new User();
		}
		$user->fill($request);
		$user->token = str_random(50);
		if(isset($request["password"]) && $request["password"] != null) {
            $user->password = bcrypt($request["password"]);
        }
        $user->save();
		\Mail::to($user->email)->send(new UserMail($user->token));
		return $user;
    }
    //validation on rules
    public static function validationRules($id = null)
    {
        //$phoneRegex = "/^\+1\(?([0-9]{3})\)-[0-9]{3}-[0-9]{4}$/";
        return [
            'first_name' => ['required', 'max:255'], 
            'last_name' => ['required', 'max:255'],
            'email' => 'required|email|unique:users,email,' .$id,
            'password' =>'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|',
            'contact_number' => 'required|regex:/^\+1\(?([0-9]{3})\)-[0-9]{3}-[0-9]{4}$/|unique:users,contact_number,' .$id, 
            ];
    }
    
    //Update case validation Rule
    
    /*public static function validationEmailIgnor($id)
    {
        $phoneRegex = "/^\+1\(?([0-9]{3})\)-[0-9]{3}-[0-9]{4}$/";
        return [
            'first_name' => ['required', 'max:255'], 
            'last_name' => ['required', 'max:255'],
            'password' =>'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|',
            'contact_number' =>'required|regex:/^\+1\(?([0-9]{3})\)-[0-9]{3}-[0-9]{4}$/|unique:users,contact_number,' .$id,
            ];
    }*/
    //user update data 
    public function updateUser($request)
	{
		self::store($request, $this);
	}
	//update status not_active to active
	public function statusUpdate($verifyUser)
    {
        $verifyUser->status = 'active';
	    $verifyUser->update();
    }
    
    public static function getUser($request)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
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
    }
    
    public static function logout($request)
    {
        try {
                JWTAuth::invalidate($request->token);
                return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
                ]);
            } catch (JWTException $exception) 
            {
                return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
                ], 500);
            }
    }
}
