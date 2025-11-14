<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Retrieve all categories along with their child categories.
     *
     * Returns a list of all categories from the database.
     * Each category includes its nested children via the `children` relationship.
     *
     * Useful for building category trees, menus, and hierarchical listings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCategories() {
        $categories = Category::with('children')->get();
        return response()->json($categories);
    }
}
