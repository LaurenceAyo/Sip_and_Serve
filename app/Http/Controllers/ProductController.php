<?php

namespace App\Http\Controllers;

use App\Models\MenuItem as MenuItemModel;
use App\Models\Ingredient;
use App\Models\MenuItemIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $menu_items = MenuItemModel::with('ingredients')->get();
        $ingredients = Ingredient::all();
        return view('profile.product', compact('menu_items', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $categoryId = 1;
            if ($request->category && $request->category !== 'NULL' && is_numeric($request->category)) {
                $categoryId = (int)$request->category;
            }

            $menuItem = MenuItemModel::create([
                'name' => $request->name,
                'price' => $request->price,
                'cost' => $this->calculateCost($request->ingredients),
                'category_id' => $categoryId,
                'description' => $request->description ?? ''
            ]);

            foreach ($request->ingredients as $ingredient) {
                MenuItemIngredient::create([
                    'menu_item_id' => $menuItem->id,
                    'ingredient_id' => $ingredient['id'],
                    'quantity_needed' => $ingredient['quantity']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Update request data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $menuItem = MenuItemModel::findOrFail($id);

            $categoryId = 1;
            if ($request->category && $request->category !== 'NULL' && is_numeric($request->category)) {
                $categoryId = (int)$request->category;
            }

            $menuItem->update([
                'name' => $request->name,
                'price' => $request->price,
                'cost' => $this->calculateCost($request->ingredients),
                'category_id' => $categoryId,
                'description' => $request->description ?? ''
            ]);

            MenuItemIngredient::where('menu_item_id', $id)->delete();

            foreach ($request->ingredients as $ingredient) {
                MenuItemIngredient::create([
                    'menu_item_id' => $id,
                    'ingredient_id' => $ingredient['id'],
                    'quantity_needed' => $ingredient['quantity']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $menuItem = MenuItemModel::findOrFail($id);
            MenuItemIngredient::where('menu_item_id', $id)->delete();
            $menuItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateCost($ingredients)
    {
        $totalCost = 0;
        foreach ($ingredients as $ingredient) {
            $ing = Ingredient::find($ingredient['id']);
            if ($ing) {
                $totalCost += ($ing->cost_per_unit * $ingredient['quantity']);
            }
        }
        return round($totalCost, 2);
    }
}