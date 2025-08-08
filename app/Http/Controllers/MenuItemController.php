<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuItemController extends Controller
{
    public function store(Request $request) {
    $item = DB::table('menu_items')->insertGetId([
        'name' => $request->name,
        'price' => $request->price,
        'created_at' => now(),
    ]);
    
    $newItem = DB::table('menu_items')->find($item);
    
    return response()->json([
        'success' => true, 
        'item' => $newItem
    ]);
}

public function update(Request $request) {
    DB::table('menu_items')
        ->where('id', $request->id)
        ->update(['price' => $request->price]);
    
    return response()->json(['success' => true]);
}

public function delete(Request $request) {
    DB::table('menu_items')
        ->where('id', $request->id)
        ->delete();
    
    return response()->json(['success' => true]);
}
};