<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class TowerController extends Controller
{
    public function index()
    {
        // Ambil semua artikel dengan category 'tower'
        $articles = Article::where('category', 'tower')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('products.tower', compact('articles'));
    }
    
    public function show($slug)
    {
        // Ambil artikel berdasarkan slug
        $article = Article::where('slug', $slug)
                         ->where('category', 'tower')
                         ->firstOrFail();
        
        return view('products.tower-detail', compact('article'));
    }
}