<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Support\Str;
use App\Services\ImageService;


class NewsService
{
    public function getAll()
    {
        return News::latest()->get();
    }

    public function find($id)
    {
        return News::findOrFail($id);
    }

    public function create($request)
    {
        $data = $request->validate([
            'title' => 'required',
            'summary' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'content' => 'nullable',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        // upload gambar
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        // sumber default = manual
        $data['source'] = 'manual';

        return News::create($data);
    }

   public function createImported($data)
    {
        \Log::info('Creating imported news', [
            'title' => $data['title'],
            'image' => $data['image'] ?? 'NULL',
            'source' => $data['source'],
        ]);

        // ✅ SCRAPER SUDAH DOWNLOAD, JADI LANGSUNG SIMPAN
        return News::create([
            'title'        => $data['title'],
            'summary'      => $data['summary'] ?? null,
            'image'        => $data['image'] ?? null, // Sudah berupa path lokal dari scraper
            'source'       => $data['source'],
            'link'         => $data['link'],
            'published_at' => $data['published_at'] ?? now(),
            'is_active'    => true,
        ]);
    }


    public function delete($id)
    {
        return News::findOrFail($id)->delete();
    }
}
