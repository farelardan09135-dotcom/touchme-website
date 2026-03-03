<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Redirect admin ke dashboard
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Ambil berita aktif, urutkan terbaru, maksimal 5 untuk slide
        $news = News::where('is_active', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('home', compact('news'));
    }
}