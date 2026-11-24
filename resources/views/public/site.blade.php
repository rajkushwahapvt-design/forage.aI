<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $site->seo_metadata['title'] ?? $site->title }}</title>
    <meta name="description" content="{{ $site->meta_description }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {{ $site->primary_color ?? '#2563EB' }};
            --secondary: {{ $site->secondary_color ?? '#1E40AF' }};
        }
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
    </style>
    @if(!empty($site->seo_metadata['schema_org']))
        <script type="application/ld+json">
            {!! json_encode($site->seo_metadata['schema_org']) !!}
        </script>
    @endif
</head>
<body class="bg-white text-slate-800 antialiased">
    <!-- Render All Active Sections In Order -->
    @foreach($site->visibleSections as $section)
        @php
            $c = $section->content;
        @endphp

        {{-- 1. Header Section --}}
        @if($section->type === 'header')
            <nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm backdrop-blur-md bg-white/95">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <a href="#hero" class="font-extrabold text-xl tracking-tight text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-sm">
                            {{ substr($c['brand_name'] ?? $site->title, 0, 1) }}
                        </span>
                        {{ $c['brand_name'] ?? $site->title }}
                    </a>
                    <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                        @if(!empty($c['nav_links']))
                            @foreach($c['nav_links'] as $link)
                                <a href="{{ $link['url'] }}" class="hover:text-primary transition">{{ $link['label'] }}</a>
                            @endforeach
                        @endif
                    </div>
                    <div>
                        @if(!empty($c['phone']))
                            <a href="tel:{{ $c['phone'] }}" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md hover:opacity-90 transition inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-phone"></i> {{ $c['phone'] }}
                            </a>
                        @else
                            <a href="#contact" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md hover:opacity-90 transition">
                                {{ $c['cta_text'] ?? 'Contact Us' }}
                            </a>
                        @endif
                    </div>
                </div>
            </nav>
        @endif

        {{-- 2. Hero Section --}}
        @if($section->type === 'hero')
            <section id="hero" class="relative py-20 lg:py-28 bg-gradient-to-b from-slate-50 to-white overflow-hidden border-b border-slate-100">
                <div class="max-w-5xl mx-auto px-4 text-center">
                    @if(!empty($c['badge']))
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-primary border border-blue-100 text-xs font-semibold mb-6">
                            <i class="fa-solid fa-circle-check"></i> {{ $c['badge'] }}
                        </div>
                    @endif
                    <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                        {{ $c['headline'] ?? $site->title }}
                    </h1>
                    <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto mb-8 font-normal">
                        {{ $c['subheadline'] ?? '' }}
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ $c['primary_cta_link'] ?? '#services' }}" class="px-7 py-3.5 rounded-xl bg-primary text-white font-bold text-sm shadow-lg hover:opacity-95 hover:scale-105 transition-all">
                            {{ $c['primary_cta'] ?? 'Explore Offerings' }}
                        </a>
                        <a href="{{ $c['secondary_cta_link'] ?? '#contact' }}" class="px-7 py-3.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-semibold text-sm shadow-sm transition">
                            {{ $c['secondary_cta'] ?? 'Get in Touch' }}
                        </a>
                    </div>
                </div>
            </section>
        @endif

        {{-- 3. About Section --}}
        @if($section->type === 'about')
            <section id="about" class="py-16 bg-white border-b border-slate-100">
                <div class="max-w-5xl mx-auto px-4">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <h2 class="text-3xl font-bold text-slate-900 mb-3">{{ $c['heading'] ?? 'About Us' }}</h2>
                        <p class="text-slate-600 text-base leading-relaxed">{{ $c['description'] ?? '' }}</p>
                    </div>

                    @if(!empty($c['highlights']))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($c['highlights'] as $highlight)
                                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:shadow-md transition">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-primary flex items-center justify-center text-lg mb-4">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-900 mb-1">{{ $highlight['title'] }}</h3>
                                    <p class="text-xs text-slate-600">{{ $highlight['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- 4. Services / Products Section --}}
        @if($section->type === 'services')
            <section id="services" class="py-16 bg-slate-50 border-b border-slate-100">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <h2 class="text-3xl font-bold text-slate-900 mb-2">{{ $c['heading'] ?? 'Our Offerings' }}</h2>
                        <p class="text-slate-600 text-sm">{{ $c['subheading'] ?? '' }}</p>
                    </div>

                    @if(!empty($c['items']))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($c['items'] as $item)
                                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:border-primary transition flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between mb-3">
                                            <h3 class="font-bold text-slate-900 text-lg">{{ $item['name'] }}</h3>
                                            @if(!empty($item['price']))
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                                    {{ $item['price'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-600 mb-4">{{ $item['description'] ?? 'Tailored to your needs.' }}</p>
                                    </div>
                                    <a href="#contact" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                        Inquire about this &rarr;
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- 5. Testimonials Section --}}
        @if($section->type === 'testimonials')
            <section class="py-16 bg-white border-b border-slate-100">
                <div class="max-w-5xl mx-auto px-4">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-slate-900 mb-2">{{ $c['heading'] ?? 'Client Reviews' }}</h2>
                        <p class="text-xs text-slate-500">Real verified feedback from clients</p>
                    </div>
                    @if(!empty($c['reviews']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($c['reviews'] as $review)
                                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="flex items-center gap-1 text-amber-400 text-xs mb-3">
                                        @for($i = 0; $i < ($review['rating'] ?? 5); $i++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-slate-700 italic mb-4">"{{ $review['comment'] }}"</p>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-900">{{ $review['name'] }}</span>
                                        <span class="text-slate-400">{{ $review['source'] ?? 'Verified Customer' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- 6. Gallery Section --}}
        @if($section->type === 'gallery')
            <section id="gallery" class="py-16 bg-slate-50 border-b border-slate-100">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-slate-900 mb-2">{{ $c['heading'] ?? 'Photo Showcase' }}</h2>
                    </div>
                    @if(!empty($c['images']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($c['images'] as $img)
                                <div class="group relative rounded-2xl overflow-hidden shadow-sm bg-slate-200 aspect-video">
                                    <img src="{{ $img['url'] }}" alt="{{ $img['caption'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @if(!empty($img['caption']))
                                        <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/70 to-transparent text-white text-xs font-medium">
                                            {{ $img['caption'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- 7. Hours & Location Section --}}
        @if($section->type === 'hours_location')
            <section class="py-16 bg-white border-b border-slate-100">
                <div class="max-w-5xl mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-slate-900 mb-4">{{ $c['heading'] ?? 'Visit or Contact Us' }}</h2>
                            <div class="space-y-4 text-sm text-slate-700">
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-location-dot text-primary text-base mt-1"></i>
                                    <div>
                                        <strong class="block text-slate-900 font-semibold">Address</strong>
                                        <span>{{ $c['address'] ?? 'Address on request' }}</span>
                                    </div>
                                </div>
                                @if(!empty($c['phone']))
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-phone text-primary text-base mt-1"></i>
                                        <div>
                                            <strong class="block text-slate-900 font-semibold">Phone</strong>
                                            <a href="tel:{{ $c['phone'] }}" class="text-primary hover:underline">{{ $c['phone'] }}</a>
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($c['email']))
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-envelope text-primary text-base mt-1"></i>
                                        <div>
                                            <strong class="block text-slate-900 font-semibold">Email</strong>
                                            <a href="mailto:{{ $c['email'] }}" class="text-primary hover:underline">{{ $c['email'] }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                            <h3 class="font-bold text-slate-900 text-base mb-3 flex items-center gap-2">
                                <i class="fa-regular fa-clock text-primary"></i> Operating Hours
                            </h3>
                            @if(!empty($c['hours']) && is_array($c['hours']))
                                <div class="space-y-2 text-xs">
                                    @foreach($c['hours'] as $day => $time)
                                        <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                                            <span class="font-medium text-slate-600">{{ $day }}</span>
                                            <span class="font-bold text-slate-900">{{ $time }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- 8. Contact & Inquiry Form --}}
        @if($section->type === 'contact')
            <section id="contact" class="py-16 bg-slate-50 border-b border-slate-100">
                <div class="max-w-3xl mx-auto px-4">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-slate-900 mb-2">{{ $c['heading'] ?? 'Send Us a Message' }}</h2>
                        <p class="text-sm text-slate-600">{{ $c['subheading'] ?? 'Fill out the form below and we will contact you promptly.' }}</p>
                    </div>

                    @if(session('inquiry_success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-center text-sm font-semibold">
                            <i class="fa-solid fa-circle-check text-emerald-600 mr-2"></i> {{ session('inquiry_success') }}
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200">
                        <form action="{{ route('public.site.inquiry', $site->slug) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your Name *</label>
                                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your Phone</label>
                                    <input type="text" name="phone" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Your Email</label>
                                <input type="email" name="email" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Your Message or Inquiry *</label>
                                <textarea name="message" required rows="4" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:opacity-90 transition">
                                {{ $c['submit_button_text'] ?? 'Send Message' }}
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        @endif

        {{-- 9. Footer Section --}}
        @if($section->type === 'footer')
            <footer class="bg-slate-900 text-slate-400 py-10">
                <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                    <div>
                        <p>{{ $c['copyright'] ?? ('© ' . date('Y') . " {$site->title}. All rights reserved.") }}</p>
                        <p class="text-slate-500 mt-1">{{ $c['disclaimer'] ?? 'Powered by AI Builds My Website' }}</p>
                    </div>
                    @if(!empty($c['social_links']))
                        <div class="flex items-center gap-4 text-base">
                            @foreach($c['social_links'] as $platform => $url)
                                @if(!empty($url))
                                    <a href="{{ $url }}" target="_blank" class="hover:text-white transition">
                                        <i class="fa-brands fa-{{ $platform }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </footer>
        @endif
    @endforeach
    @if(!empty($site->business->phone))
        <!-- Floating WhatsApp Contact Button -->
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $site->business->phone) }}?text={{ urlencode('Hi ' . $site->title . ', I saw your website and would like to inquire.') }}" target="_blank" class="fixed bottom-6 right-6 z-50 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-full shadow-lg shadow-emerald-500/30 flex items-center gap-2 font-bold text-xs transition transform hover:scale-105" title="Direct WhatsApp Chat">
            <i class="fa-brands fa-whatsapp text-lg"></i>
            <span class="hidden sm:inline">Chat on WhatsApp</span>
        </a>
    @endif
</body>
</html>
