<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
class InventoryController extends Controller
{
    public function getInventoryData()
    {
        $ingredients = DB::table('ingredients')
            ->select([
                'id',
                'name',
                'unit',
                'stock_quantity',
                'reorder_level',
                'category',
                'updated_at'
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($ingredient) {
                // Determine stock status
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
    
    public function updateIngredient(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'stock_quantity' => 'required|numeric|min:0',
            'critical_level' => 'nullable|numeric|min:0'
        ]);
        
        try {
            $updated = DB::table('ingredients')
                ->where('name', $request->name)
                ->update([
                    'stock_quantity' => $request->stock_quantity,
                    'reorder_level' => $request->critical_level ?? DB::raw('reorder_level'),
                    'updated_at' => now()
                ]);
                
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ingredient updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ingredient not found'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating ingredient: ' . $e->getMessage()
            ], 500);
        }
    }
}