<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    private function response($success, $message, $data = null, $status = 200, $errors = null)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ], $status);
    }

    private function getClient(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) return null;

        return Client::where('api_token', $token)->first();
    }

    public function index(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return $this->response(false, 'No autorizado', null, 401);
        }

        $cart = Cart::with('items.product')
            ->where('client_id', $client->id)
            ->first();

        return $this->response(true, 'Carrito obtenido correctamente', $cart);
    }

    public function add(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return $this->response(false, 'No autorizado', null, 401);
        }

        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate([
            'client_id' => $client->id
        ]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return $this->response(true, 'Producto agregado al carrito', $item);
    }

    public function remove(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return $this->response(false, 'No autorizado', null, 401);
        }

        $cart = Cart::where('client_id', $client->id)->first();

        if (!$cart) {
            return $this->response(false, 'El carrito está vacío', null, 404);
        }

        $deleted = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->delete();

        if (!$deleted) {
            return $this->response(false, 'Producto no encontrado', null, 404);
        }

        return $this->response(true, 'Producto eliminado del carrito');
    }

    public function clear(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return $this->response(false, 'No autorizado', null, 401);
        }

        $cart = Cart::where('client_id', $client->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return $this->response(true, 'Carrito vaciado correctamente');
    }
}