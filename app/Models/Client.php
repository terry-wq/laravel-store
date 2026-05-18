<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token'
    ];

    protected $hidden = ['password', 'api_token'];
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}
