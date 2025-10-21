<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $articles = $category->publishedArticles()
            ->latest()
            ->paginate(12);

        return view('categories.show', compact('category', 'articles'));
    }
}
