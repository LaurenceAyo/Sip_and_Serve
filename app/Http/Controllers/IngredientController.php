<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient; // Add your Ingredient model

class IngredientController extends Controller
{
    public function updateStock(Request $request)
    {
        $ingredient = Ingredient::where('name', $request->name)->first();
        
        if ($ingredient) {
            $ingredient->stock_quantity = $request->stock_quantity;
            if ($request->alert_level) {
                $ingredient->reorder_level = $request->alert_level;
            }
            $ingredient->save();
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Ingredient not found']);
    }
}