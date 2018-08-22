<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FacebookMessage extends Model
{
    protected $fillable = [
        'message',
        'created_at',
        'updated_at',
        ];
        
    public function sendTo()
    {
        return $this->belongsTo(User::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class);
    }
}