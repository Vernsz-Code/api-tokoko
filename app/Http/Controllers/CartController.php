<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
     public function index()
    {
        $carts = Cart::with(['user', 'product'])->get();
        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::create($request->all());
        return response()->json($cart, 201);
    }

    public function show($userId)
    {
        $carts = Cart::with(['user', 'product'])->where('users_id', $userId)->get();
        return response()->json($carts);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::findOrFail($id);
        $cart->update($request->all());
        return response()->json($cart);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();
        return response()->json(null, 204);
    }
}
