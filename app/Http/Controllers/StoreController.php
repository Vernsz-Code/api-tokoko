<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Store::all();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'uuid' => 'required|string|unique:stores,uuid',
        ]);

        $item = Store::create($validatedData);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $items = Store::where('seller_id', $id)->get();
    
        if ($items->isEmpty()) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    
        return response()->json($items);
    }
        


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'seller_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'uuid' => 'sometimes|required|string|unique:stores,uuid,'.$id,
        ]);

        $item = Store::findOrFail($id);
        $item->update($validatedData);
        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $items = Store::where('seller_id', $id);
    
        if ($items->exists()) {
            $items->delete();
            return response()->json(["message" => "success delete data"], 204);
        } else {
            return response()->json(['message' => 'Stores not found'], 404);
        }
    }
    
}
