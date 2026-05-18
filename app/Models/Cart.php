<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
    ];

    // Un carrito pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Un carrito tiene muchos items
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}