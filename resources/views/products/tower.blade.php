@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    
    <!-- Hero Section -->
    <div class="max-w-6xl mx-auto px-4 pt-8 pb-4">
        <div class="text-left">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-3">Tower</h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-3xl">
                Telecommunication tower infrastructure solutions
            </p>
        </div>
    </div>

    <!-- Tower Types Grid -->
    <div class="max-w-6xl mx-auto px-4 pb-16">
        
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @foreach($articles as $article)
                <a href="{{ route('products.tower.detail', $article->slug) }}" 
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-lg overflow-hidden transform transition-all duration-300">
                    
                    <!-- Card Header with Icon -->
                    <div class="relative h-40 bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center overflow-hidden">
                        
                        <!-- Tower Icon -->
                        <svg class="w-16 h-16 text-blue-600 dark:text-blue-400 transform transition-transform duration-300 group-hover:scale-110" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                    </div>
                    
                    <!-- Card Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">
                            {{ $article->title }}
                        </h3>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4">
                            {{ strip_tags($article->content) }}
                        </p>
                        
                        <!-- Read More Link -->
                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold text-sm group-hover:gap-3 transition-all">
                            <span>Learn More</span>
                            <svg class="w-4 h-4 transform transition-transform group-hover:translate-x-1" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
                @endforeach
                
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">
                    No Articles Available
                </h3>
                <p class="text-gray-500 dark:text-gray-500 text-sm">
                    Tower articles will be added soon
                </p>
            </div>
        @endif
        
    </div>



</div>
@endsection