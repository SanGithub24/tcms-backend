<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplaintCategory;

class CategoryController extends Controller
{
    // Fetch all Active categories (used by Tourist form)
    public function index()
    {
        $categories = ComplaintCategory::where('status', 'Active')->get();
        return response()->json($categories);
    }

    // Fetch all categories (used by Admin)
    public function adminIndex()
    {
        $categories = ComplaintCategory::all();
        return response()->json($categories);
    }

    // Create a new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:complaint_categories',
            'description' => 'nullable|string',
            'icon' => 'required|string|unique:complaint_categories',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $category = ComplaintCategory::create($request->all());

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    // Update existing category
    public function update(Request $request, $id)
    {
        $category = ComplaintCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:complaint_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'required|string|unique:complaint_categories,icon,' . $category->id,
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }
}
