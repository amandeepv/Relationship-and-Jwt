<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FacebookFriend extends Model
{
    
    protected $fillable = [
        'created_at',
        'updated_at',
        ];
    public function friend()
    {
        return $this->belongsTo(User::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}