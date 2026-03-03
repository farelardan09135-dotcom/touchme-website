<x-guest-layout>
    <div class="h-screen flex bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">

        {{-- LEFT SIDE --}}
        <div class="w-full md:w-1/2 flex flex-col justify-center px-6 md:px-16 py-12">
            <div class="max-w-md mx-auto w-full">
                
                <div class="flex justify-center mb-2">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full blur-xl opacity-30"></div>
                        <img src="/images/touchme-logo.png" alt="TouchMe" class="relative h-16 w-auto">
                    </div>
                </div>
                <div class="text-center mb-4">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-1">
                        Enter Your Learning Area👋
                    </h1>
                    <p class="text-gray-600">
                        Let’s continue building your future!
                    </p>
                </div>  

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 animate-shake">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-red-800">Login Failed!</p>
                                <p class="text-sm text-red-600">
                                    {{ $errors->first('email') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                        <p class="text-sm text-green-600">{{ session('status') }}</p>
                    </div>
                @endif

                 {{-- Login Form --}}
                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block font-semibold text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full pl-12 pr-4 py-3 border @error('email') border-red-500 @enderror rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 outline-none"
                                placeholder="you@example.com"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block font-semibold text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password"
                                class="w-full pl-12 pr-4 py-3 border @error('password') border-red-500 @enderror rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 outline-none"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        {{-- Remember Me & Forgot Password --}}
                        <div class="flex items-center justify-between mt-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline transition">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <button 
                            type="submit"
                            class="group relative w-full mt-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3.5 rounded-xl font-semibold shadow-lg shadow-blue-500/50 hover:shadow-xl hover:shadow-blue-500/60 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200"
                            >
                            <span class="flex items-center justify-center">
                                Sign In
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </button>
                </form>

                    {{-- Register Link --}}
                    @if (Route::has('register'))
                        <p class="mt-8 text-center text-sm text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition">
                                Sign up for free
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="hidden md:flex w-1/2 relative overflow-hidden group cursor-pointer rounded-2xl shadow-xl">

            {{-- Gradient Overlay (muncul saat hover) --}}
            <div class="absolute inset-0 bg-gradient-to-br from-blue-200/60 to-purple-400/40 
                z-20 opacity-0 transition-opacity duration-700 group-hover:opacity-100">
            </div>

            {{-- Background Image - slide up on hover (kayak versi pertama) --}}
            <img src="/images/mitratel-bg.png"
                class="w-full h-full object-cover transition-transform duration-700 ease-in-out 
                group-hover:-translate-y-full"
                alt="Background">

            {{-- Main Content - naik dari bawah + fade in --}}
            <div class="absolute inset-0 z-30 flex flex-col justify-start pt-20 px-16 text-white 
                transform translate-y-full opacity-0 
                transition-all duration-700 ease-in-out group-hover:translate-y-0 group-hover:opacity-100">

                {{-- Logo --}}
                <img src="/images/touchme-logo.png"
                    class="h-20 w-auto mx-auto mb-6"
                    alt="Logo">

                {{-- Title --}}
                <h2 class="text-4xl lg:text-5xl text-gray-700 font-bold leading-tight text-center mb-3 whitespace-nowrap">
                    Potential Everywhere
                </h2>

                {{-- Subtitle --}}
                <p class="text-lg lg:text-xl text-gray-700 text-center leading-relaxed">
                    Membangun konektivitas, menyatukan bangsa
                </p>
            </div>

            {{-- Achievements - muncul setelah content (delay) --}}
            <div class="absolute bottom-0 left-0 right-0 z-40 px-14 pb-12 
                transform translate-y-full opacity-0 
                transition-all duration-700 ease-in-out delay-200 
                group-hover:translate-y-0 group-hover:opacity-100">

                <h3 class="text-2xl font-semibold text-center mb-6">
                    Our Achievements
                </h3>

                <div class="grid grid-cols-3 gap-10 text-center">

                    <div class="transition duration-300 hover:scale-110">
                        <div class="text-4xl font-bold mb-1">1000+</div>
                        <div class="text-sm opacity-90">Active Learners</div>
                    </div>

                    <div class="transition duration-300 hover:scale-110">
                        <div class="text-4xl font-bold mb-1">500+</div>
                        <div class="text-sm opacity-90">Courses</div>
                    </div>

                    <div class="transition duration-300 hover:scale-110">
                        <div class="text-4xl font-bold mb-1">95%</div>
                        <div class="text-sm opacity-90">Satisfaction</div>
                    </div>
                </div>
            </div>

            {{-- Decorative Circles --}}
            <div class="absolute top-12 right-12 w-56 h-56 bg-white/10 rounded-full 
                blur-3xl opacity-0 transition-opacity duration-700 group-hover:opacity-100">
            </div>

            <div class="absolute bottom-16 left-16 w-72 h-72 bg-purple-300/10 
                rounded-full blur-3xl opacity-0 transition-opacity duration-700 group-hover:opacity-100">
            </div>

        </div>
    </div>

    {{-- Custom Animations --}}
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</x-guest-layout>
