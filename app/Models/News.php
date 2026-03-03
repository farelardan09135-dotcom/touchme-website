<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * News Model
 * 
 * Model untuk mengelola data berita dari berbagai sumber
 */
class News extends Model
{
    use HasFactory;
    
    protected $table = 'news';
    
    protected $fillable = [
        'title',
        'summary',
        'image',
        'source',
        'link',
        'content',
        'published_at',
        'is_active',
    ];
    
    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    // ✅ TAMBAHKAN ACCESSOR
    protected $appends = ['image_url', 'redirect_url', 'redirect_target'];
    
    /**
     * Get full image URL
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/default-news.jpg');
        }
        
        // Jika sudah URL
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        
        // Jika path lokal
        return asset('storage/' . $this->image);
    }
    
    /**
     * Get redirect URL based on source
     */
    public function getRedirectUrlAttribute()
    {
        // Manual: ke detail internal
        if ($this->source === 'manual') {
            return route('news.show', $this->id);
        }
        
        // External: ke link asli
        return $this->link;
    }
    
    /**
     * Get redirect target
     */
    public function getRedirectTargetAttribute()
    {
        return $this->source === 'manual' ? '_self' : '_blank';
    }
    
    /**
     * Scope untuk filter berita aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope untuk filter berdasarkan sumber
     */
    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }
}