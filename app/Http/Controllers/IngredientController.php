<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient; // Add your Ingredient model
use Illuminate\Support\Facades\DB;

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


    // Add this method to your existing IngredientController
    public function getInventoryData()
    {
        $ingredients = DB::table('ingredients')
            ->select(['id', 'name', 'unit', 'stock_quantity', 'reorder_level', 'category', 'updated_at'])
            ->orderBy('name')
            ->get()
            ->map(function ($ingredient) {
                $status = 'good';
                if ($ingredient->stock_quantity <= $ingredient->reorder_level) {
                    $status = 'critical';
                } elseif ($ingredient->stock_quantity <= ($ingredient->reorder_level * 2)) {
                    $status = 'low';
                }
                $ingredient->stock_status = $status;
                return $ingredient;
            });

        return response()->json([
            'success' => true,
            'data' => $ingredients
        ]);
    }
}
