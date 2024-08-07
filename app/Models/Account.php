<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    protected $fillable = ['user_id', 'account_number', 'balance'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function transitions(){
        return $this->hasMany(Transition::class, 'sender_id');
    }
    use HasFactory;
    

}