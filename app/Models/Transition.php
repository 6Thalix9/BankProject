<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'amount', 'description'];

    public function sender(){
        return $this->belongsTo(Account::class, 'sender_id');
    }
    public function receiver(){
        return $this->belongsTo(Account::class, 'receiver_id');
    }
    public function account(){
        return $this->belongsTo(Account::class);
    }
    use HasFactory;
}