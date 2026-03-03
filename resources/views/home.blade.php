@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">

<!-- Banner News Slider -->
<div class="w-full max-w-6xl mx-auto mt-6 px-4">

    <div class="swiper mySwiper rounded-3xl overflow-hidden shadow-lg">

        <div class="swiper-wrapper">

            @foreach($news as $item)
                <div class="swiper-slide relative">

                   <img 
                    src="{{ $item->image_url }}"
                    class="rounded-xl w-full h-96 object-cover"
                    alt="{{ $item->title }}"
                    >

                    <!-- Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 
                        bg-gradient-to-t from-black/90 via-black/40 to-transparent">

                        <h2 class="text-white text-2xl md:text-3xl font-bold mb-3">
                            {{ $item->title }}
                        </h2>

                        <p class="text-gray-200 text-sm md:text-base line-clamp-2 mb-4">
                            {{ $item->summary }}
                        </p>

                        <a href="{{ $item->redirect_url }}"
                        target="{{ $item->redirect_target }}"
                        class="text-blue-300 font-semibold text-sm flex items-center gap-2">
                            Read more →
                        </a>


                    </div>

                </div>
            @endforeach

        </div>

        <div class="swiper-pagination"></div>
    </div>
</div>


    <!-- Product Mitratel -->
    <div class="max-w-6xl mx-auto mt-16 px-4">
        <div class="mb-8">
            <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-3">Product Mitratel</h2>
            <p class="text-gray-600 dark:text-gray-400">Innovative telecommunications solutions for your business</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            
            @php
            $products = [
                [
                    'name' => 'Tower',
                    'route' => 'products.tower',
                    'description' => 'Telecommunication tower infrastructure solutions',
                    'color' => 'blue',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>'
                ],
                [
                    'name' => 'Fiber Optic',
                    'route' => 'products.fiber',
                    'description' => 'High-speed fiber optic network solutions',
                    'color' => 'purple',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
                ],
                [
                    'name' => 'Managed Service',
                    'route' => 'products.managed-service',
                    'description' => 'Comprehensive managed service solutions',
                    'color' => 'green',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                ],
                [
                    'name' => 'Administration',
                    'route' => 'products.administration',
                    'description' => 'Administrative and management solutions',
                    'color' => 'orange',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                ],
                [
                    'name' => 'Power',
                    'route' => 'products.power',
                    'description' => 'Power supply and energy management',
                    'color' => 'red',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
                ]
            ];
            @endphp

            @foreach($products as $product)
            <a href="{{ route($product['route']) }}" class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-{{ $product['color'] }}-500 to-{{ $product['color'] }}-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Content -->
                <div class="relative p-6 z-10">
                    <div class="mb-4 text-{{ $product['color'] }}-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $product['icon'] !!}
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-white group-hover:text-white transition-colors duration-300">
                        {{ $product['name'] }}
                    </h3>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-white/90 transition-colors duration-300 mb-4">
                        {{ $product['description'] }}
                    </p>
                    
                    <div class="flex items-center gap-2 font-semibold text-sm text-{{ $product['color'] }}-600 group-hover:text-white transition-all duration-300">
                        Learn More
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Decorative Circle -->
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-gradient-to-br from-{{ $product['color'] }}-500 to-{{ $product['color'] }}-600 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"></div>
            </a>
            @endforeach

        </div>
    </div>

    <!-- Divisi Mitratel -->
    <div class="max-w-6xl mx-auto mt-16 px-4 pb-16">
        <div class="mb-8">
            <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-3">Divisi Mitratel</h2>
            <p class="text-gray-600 dark:text-gray-400">Explore our specialized divisions and their expertise</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            
            @php
            $divisions = [
                ['name' => 'Solution Engineering', 'slug' => 'solution-engineering', 'color' => 'from-blue-500 to-blue-600', 'members' => 24],
                ['name' => 'Construction', 'slug' => 'construction', 'color' => 'from-orange-500 to-orange-600', 'members' => 45],
                ['name' => 'OM', 'slug' => 'om', 'color' => 'from-red-500 to-red-600', 'members' => 28],
                ['name' => 'Assets', 'slug' => 'assets', 'color' => 'from-yellow-500 to-yellow-600', 'members' => 15],
            ];
            @endphp

            @foreach($divisions as $div)
            <a href="{{ route('divisions.show', $div['slug']) }}" class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                
                <!-- Gradient Background -->
                <div class="absolute inset-0 bg-gradient-to-br {{ $div['color'] }} opacity-90"></div>
                
                <!-- Content -->
                <div class="relative p-5 text-center z-10">
                    
                    <!-- Icon -->
                    <div class="mb-3 flex justify-center">
                        <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="text-base font-bold text-white mb-2">{{ $div['name'] }}</h3>

                    <!-- Members Count -->
                    <div class="flex items-center justify-center gap-1 text-white/90 text-xs mb-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>{{ $div['members'] }}</span>
                    </div>

                    <!-- Arrow on Hover -->
                    <div class="transform transition-all duration-300 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0">
                        <div class="inline-flex items-center gap-1 text-white font-semibold text-xs">
                            View Materials
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Shine Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 transition-transform duration-700 -translate-x-full group-hover:translate-x-full"></div>
            </a>
            @endforeach

        </div>
    </div>

</div>
@endsection
