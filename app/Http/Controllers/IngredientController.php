<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;

class IngredientController extends Controller
{
    public function updateStock(Request $request)
    {
        // Find inventory record by ingredient name
        $inventory = Inventory::whereHas('ingredient', function ($query) use ($request) {
            $query->where('name', $request->name);
        })->first();

        if ($inventory) {
            // Add to current stock (restocking)
            $inventory->current_stock += $request->current_stock;

            if ($request->minimum_stock) {
                $inventory->minimum_stock = $request->minimum_stock;
            }
            $inventory->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Item not found']);
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
