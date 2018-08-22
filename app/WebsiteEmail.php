<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Website;

class WebsiteEmail extends Model
{
    protected $fillable = [
            'email',
            'created_at',
            'updated_at'
        ];
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
    
    public static function store($request, $website, $websiteEmail = null)
    {$w = [];
        foreach($request as $a){
    	    $websiteEmail = new WebsiteEmail();
    		$websiteEmail->fill($a);
    		$websiteEmail->website()->associate($website);
    		$websiteEmail->save();
        }
    }
}
