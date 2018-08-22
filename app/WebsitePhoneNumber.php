<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Website;

class WebsitePhoneNumber extends Model
{
     protected $fillable = [
            'phone_number',
            'created_at',
            'updated_at'
        ];
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
    
    public static function store($request, $website, $websitePhoneNumber = null)
    {$w = [];
        foreach($request as $a){
    	    $websitePhoneNumber = new WebsitePhoneNumber();
    		$websitePhoneNumber->fill($a);
    		$websitePhoneNumber->website()->associate($website);
    		$websitePhoneNumber->save();
        }
    }
}
