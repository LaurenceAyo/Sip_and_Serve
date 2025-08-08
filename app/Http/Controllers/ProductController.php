<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
{
    $menu_items = MenuItem::all();  // Change this line
    return view('profile.product', compact('menu_items'));  // And this line
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        MenuItem::create([
            'name' => $request->name,
            'price' => $request->price,
            'cost' => $request->price * 0.6, // default cost calculation
            'category' => 'NULL',
            'description' => ''
        ]);

        return redirect()->route('products')->with('success', 'Product added successfully!');
    }

    public function update(Request $request, MenuItem $product)
    {
        $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        $product->update(['price' => $request->price]);

        return redirect()->route('products')->with('success', 'Product price updated successfully!');
    }

    public function destroy(MenuItem $product)
    {
        $product->delete();
        return redirect()->route('products')->with('success', 'Product deleted successfully!');
    }
}