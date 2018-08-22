<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\WebsitePhoneNumber;
use App\WebsiteEmail;

class Website extends Model
{
    protected $fillable = [
            'uuid',
            'name',
            'status',
        ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function websitePhoneNumber()
    {
        return $this->hasMany(WebsitePhoneNumber::class);
    }
    
    public function websiteEmail()
    {
        return $this->hasMany(WebsiteEmail::class);
    }
        
    public static function store($request, $website = null)
    {
		if($website === null){
			$website = new Website();
		}
		$website->fill($request);
		$user = User::findOrFail($request["user"]["id"]);
		$website->user()->associate($user);
        $website->save();
        if($request["phone_numbers"]){
            WebsitePhoneNumber::store($request["phone_numbers"], $website);
        }
        if($request["emails"]){
            WebsiteEmail::store($request["emails"] , $website);
        }
		return $website;
    }
    
    public static function validationRules($id = null)
    {
        return [
            'name' => 'required|unique:websites', 
            'uuid' => ['required']
            ];
    }
    
    public function updateWebsite($request)
	{
		self::store($request, $this);
	}
}
