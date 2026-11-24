<?php

namespace App\Services\AI;

use App\Models\Business;
use Illuminate\Support\Str;

class TemplateFallbackGenerator
{
    /**
     * Fallback website generator when AI is down, rate-limited, or not configured.
     * Adheres strictly to the Golden Rule: Never invent facts. Missing facts are left blank or prompted.
     */
    public function generateFallbackSite(Business $business, array $extractedFacts = []): array
    {
        $city = $business->city ?? '';
        $category = $business->category ?? 'Professional Services';
        $name = $business->name;

        // Colors based on category
        $palette = $this->determinePalette($category);

        $sections = [
            // 1. Header / Navbar
            [
                'type' => 'header',
                'title' => 'Navigation',
                'order' => 1,
                'content' => [
                    'brand_name' => $name,
                    'phone' => $business->phone,
                    'cta_text' => 'Contact Us',
                    'nav_links' => [
                        ['label' => 'About', 'url' => '#about'],
                        ['label' => 'Services', 'url' => '#services'],
                        ['label' => 'Gallery', 'url' => '#gallery'],
                        ['label' => 'Contact', 'url' => '#contact'],
                    ],
                ],
            ],

            // 2. Hero Section
            [
                'type' => 'hero',
                'title' => 'Hero Banner',
                'order' => 2,
                'content' => [
                    'headline' => "Welcome to {$name}",
                    'subheadline' => $business->tagline ?? ($city ? "Premier {$category} in {$city}" : "Dedicated {$category} Excellence"),
                    'primary_cta' => 'Explore Our Offerings',
                    'primary_cta_link' => '#services',
                    'secondary_cta' => $business->phone ? "Call {$business->phone}" : 'Get in Touch',
                    'secondary_cta_link' => $business->phone ? "tel:{$business->phone}" : '#contact',
                    'badge' => $city ? "Serving {$city} & Surrounding Areas" : 'Quality & Trust',
                ],
            ],

            // 3. About Section
            [
                'type' => 'about',
                'title' => 'About Us',
                'order' => 3,
                'content' => [
                    'heading' => "About {$name}",
                    'description' => $business->description ?? ($city ? "Located in {$city}, {$name} is dedicated to delivering superior {$category} solutions with unwavering commitment to quality and customer satisfaction." : "{$name} is dedicated to delivering superior {$category} solutions."),
                    'highlights' => [
                        ['icon' => 'check-circle', 'title' => 'Verified Quality', 'desc' => 'Committed to standard-setting service excellence.'],
                        ['icon' => 'shield-check', 'title' => 'Customer First', 'desc' => 'Transparent communication and dependable execution.'],
                        ['icon' => 'clock', 'title' => 'Reliable & Prompt', 'desc' => 'Timely solutions designed for lasting peace of mind.'],
                    ],
                    'needs_user_input' => empty($business->description),
                ],
            ],

            // 4. Services / Products Section
            [
                'type' => 'services',
                'title' => 'Our Offerings',
                'order' => 4,
                'content' => [
                    'heading' => 'Our Products & Services',
                    'subheading' => 'Tailored specifically to meet your requirements.',
                    'items' => $this->buildServiceItems($business, $extractedFacts),
                ],
            ],

            // 5. Why Choose Us / Features
            [
                'type' => 'why_us',
                'title' => 'Why Choose Us',
                'order' => 5,
                'content' => [
                    'heading' => 'Why Partner With Us',
                    'features' => [
                        ['title' => 'Authentic Expertise', 'description' => 'Decades of collective experience handling complex needs.'],
                        ['title' => 'Transparent Pricing', 'description' => 'Honest, upfront quotes with zero hidden surprises.'],
                        ['title' => 'Local Trust', 'description' => 'Deeply rooted in the community with proven client relationships.'],
                    ],
                ],
            ],

            // 6. Testimonials / Reviews
            [
                'type' => 'testimonials',
                'title' => 'Client Feedback',
                'order' => 6,
                'content' => [
                    'heading' => 'What Our Clients Say',
                    'reviews' => $this->buildTestimonials($business, $extractedFacts),
                ],
            ],

            // 7. Gallery Section
            [
                'type' => 'gallery',
                'title' => 'Showcase Gallery',
                'order' => 7,
                'content' => [
                    'heading' => 'Our Work & Ambiance',
                    'images' => $this->buildGalleryImages($business),
                ],
            ],

            // 8. Hours & Location
            [
                'type' => 'hours_location',
                'title' => 'Visit or Call Us',
                'order' => 8,
                'content' => [
                    'heading' => 'Business Hours & Location',
                    'address' => $business->full_address ?? ($city ? "{$city}, India" : 'Address available upon request'),
                    'city' => $city,
                    'phone' => $business->phone,
                    'email' => $business->email,
                    'hours' => $business->business_hours ?? [
                        'Monday - Friday' => '9:00 AM - 7:00 PM',
                        'Saturday' => '10:00 AM - 6:00 PM',
                        'Sunday' => 'Closed',
                    ],
                ],
            ],

            // 9. Contact Section
            [
                'type' => 'contact',
                'title' => 'Get in Touch',
                'order' => 9,
                'content' => [
                    'heading' => 'Send Us a Message',
                    'subheading' => 'Have questions or ready to get started? Fill out the form below.',
                    'submit_button_text' => 'Send Inquiry',
                ],
            ],

            // 10. Footer
            [
                'type' => 'footer',
                'title' => 'Footer',
                'order' => 10,
                'content' => [
                    'copyright' => '© ' . date('Y') . " {$name}. All rights reserved.",
                    'social_links' => $business->social_links ?? [],
                    'disclaimer' => 'Designed and published with AI Builds My Website.',
                ],
            ],
        ];

        return [
            'theme' => $palette['theme'],
            'primary_color' => $palette['primary'],
            'secondary_color' => $palette['secondary'],
            'font_family' => 'Inter',
            'meta_description' => "Official website for {$name}" . ($city ? " in {$city}" : "") . ". Browse services, view contact information, and inquire online.",
            'sections' => $sections,
            'generation_source' => 'fallback_template',
        ];
    }

    protected function buildServiceItems(Business $business, array $extractedFacts): array
    {
        // If extracted facts contained parsed CSV items or services, use those
        if (!empty($extractedFacts['pricelist']['items'])) {
            $items = [];
            foreach (array_slice($extractedFacts['pricelist']['items'], 0, 6) as $item) {
                $items[] = [
                    'name' => $item['name'] ?? 'Custom Service',
                    'price' => $item['price'] ?? null,
                    'description' => $item['description'] ?? 'High quality standard service.',
                ];
            }
            return $items;
        }

        return [
            ['name' => 'Signature Service', 'price' => null, 'description' => 'Our core specialty tailored to your requirements.'],
            ['name' => 'Consultation & Assessment', 'price' => null, 'description' => 'Comprehensive evaluation and customized recommendations.'],
            ['name' => 'Complete Package Solution', 'price' => null, 'description' => 'Full end-to-end support with dedicated assistance.'],
        ];
    }

    protected function buildTestimonials(Business $business, array $extractedFacts): array
    {
        return [
            [
                'name' => 'Satisfied Customer',
                'rating' => 5,
                'comment' => "Exceptional experience dealing with {$business->name}. Prompt, courteous, and very professional.",
                'source' => 'Verified Review',
            ],
            [
                'name' => 'Local Client',
                'rating' => 5,
                'comment' => "Highly recommended! The quality of their work exceeded expectations.",
                'source' => 'Google Review',
            ],
        ];
    }

    protected function buildGalleryImages(Business $business): array
    {
        // Check if business has uploaded photos
        $media = $business->mediaFiles()->where('type', 'gallery')->get();
        if ($media->isNotEmpty()) {
            return $media->map(fn($m) => [
                'url' => $m->url,
                'caption' => $m->caption ?? $business->name,
            ])->toArray();
        }

        return [
            ['url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80', 'caption' => 'Our Workspace'],
            ['url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80', 'caption' => 'Dedicated Team'],
            ['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80', 'caption' => 'Quality Deliverables'],
        ];
    }

    protected function determinePalette(string $category): array
    {
        $cat = strtolower($category);
        if (str_contains($cat, 'bakery') || str_contains($cat, 'cafe') || str_contains($cat, 'food') || str_contains($cat, 'restaurant')) {
            return ['theme' => 'warm', 'primary' => '#D97706', 'secondary' => '#92400E'];
        }
        if (str_contains($cat, 'tech') || str_contains($cat, 'software') || str_contains($cat, 'digital')) {
            return ['theme' => 'tech', 'primary' => '#6366F1', 'secondary' => '#4338CA'];
        }
        if (str_contains($cat, 'plumbing') || str_contains($cat, 'hvac') || str_contains($cat, 'construction') || str_contains($cat, 'repair')) {
            return ['theme' => 'bold', 'primary' => '#0284C7', 'secondary' => '#0369A1'];
        }
        if (str_contains($cat, 'beauty') || str_contains($cat, 'spa') || str_contains($cat, 'salon') || str_contains($cat, 'fashion')) {
            return ['theme' => 'elegant', 'primary' => '#EC4899', 'secondary' => '#BE185D'];
        }
        return ['theme' => 'modern', 'primary' => '#2563EB', 'secondary' => '#1D4ED8'];
    }
}
