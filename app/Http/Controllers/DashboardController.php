<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalNews = News::count();
        $activeNews = News::where('is_active', true)->count();
        $manualNews = News::where('source', 'manual')->count();
        $importedNews = News::whereIn('source', ['mitratel', 'instagram'])->count();

        return view('admin.dashboard', compact(
            'totalNews',
            'activeNews', 
            'manualNews',
            'importedNews'
        ));
    }
}