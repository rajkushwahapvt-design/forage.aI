<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'AI Builds My Website') }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">
    <!-- Global Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('builder.index') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-wand-magic-sparkles text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">AI Builds My Website</span>
                    <span class="hidden sm:inline-block ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">Websites.co.in</span>
                </div>
            </a>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('builder.index') }}" class="text-slate-600 hover:text-slate-900 font-medium px-3 py-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-plus mr-1"></i> New Website
                </a>
                <a href="https://github.com" target="_blank" class="text-slate-500 hover:text-slate-900 text-lg px-2">
                    <i class="fa-brands fa-github"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                    <div class="font-semibold mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Please fix the following:
                    </div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-12">
        <p>Built for the Websites.co.in Full Stack Development Assignment &bull; Laravel + MySQL + Gemini AI</p>
    </footer>

    @yield('scripts')
</body>
</html>
