<?php

namespace App\Http\Controllers\Api;

use App\Models\Faqs;
use App\Models\News;
use App\Models\Unit;
use App\Models\Project;
use App\Models\Facility;
use App\Models\ConceptPage;
use App\Models\HomeFeature;
use App\Models\SocialMedia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Accessibility;
use App\Models\CompanyProfile;
use App\Models\ContactMessage;
use App\Models\HomePageSetting;
use App\Models\ProjectCategory;
use App\Models\HomeFeaturedUnit;
use App\Models\AccessibilityPage;
use App\Models\ConceptPageSection;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class LandingPageController extends Controller
{
    /**
     * Get company profile information
     */
    public function getCompanyProfile()
    {
        try {
            $profile = CompanyProfile::first();
            $socialMedias = SocialMedia::active()->ordered()->get();

            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Company profile tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Company profile berhasil diambil',
                'data' => [
                    'company' => [
                        'pt_name' => $profile->pt_name,
                        'company_name' => $profile->company_name,
                        'address' => $profile->address,
                        'email' => $profile->email,
                        'phone' => $profile->phone,
                        'formatted_phone' => $profile->formatted_phone,
                        'whatsapp_url' => $profile->whatsapp_url,
                        'map_embed' => $profile->map_embed,
                        'logo_url' => $profile->logo_url,
                    ],
                    'social_medias' => $socialMedias->map(function ($social) {
                        return [
                            'id' => $social->id,
                            'platform' => $social->platform,
                            'url' => $social->url,
                            'platform_icon' => $social->platform_icon,
                            'platform_color' => $social->platform_color,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data company profile',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get home page content
     */
    public function getHomePage()
    {
        try {
            $homePage = HomePageSetting::active()->first();
            $features = HomeFeature::active()->ordered()->get();
            $featuredUnits = HomeFeaturedUnit::active()->ordered()->with(['unit.project'])->get();
            $latestNewsFirst = News::published()->latest()->first();


            if (!$homePage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Home page tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Home page berhasil diambil',
                'data' => [
                    'banner' => [
                        'type' => $homePage->banner_type,
                        'image_url' => $homePage->banner_image_url,
                        'video_url' => $homePage->banner_video_url,
                        'alt_text' => $homePage->banner_alt_text,
                    ],
                    'hero' => [
                        'title' => $homePage->hero_title,
                        'description' => $homePage->hero_description,
                    ],
                    'about' => [
                        'section_title' => $homePage->about_section_title,
                        'title' => $homePage->about_title,
                        'description' => $homePage->about_description,
                        'image_url' => $homePage->about_image_url,
                        'image_alt_text' => $homePage->about_image_alt_text,
                        'link_text' => $homePage->about_link_text,
                        'link_url' => $homePage->about_link_url,
                    ],
                    'features_section' => [
                        'section_title' => $homePage->features_section_title,
                        'title' => $homePage->features_title,
                        'description' => $homePage->features_description,
                        'image_url' => $homePage->features_image_url,
                        'image_alt_text' => $homePage->features_image_alt_text,
                        'link_text' => $homePage->features_link_text,
                        'link_url' => $homePage->features_link_url,
                        'features' => $features->map(function ($feature) {
                            return [
                                'id' => $feature->id,
                                'icon' => $feature->icon,
                                'title' => $feature->title,
                                'description' => $feature->description,
                            ];
                        })
                    ],
                    'location' => [
                        'section_title' => $homePage->location_section_title,
                        'title' => $homePage->location_title,
                        'description' => $homePage->location_description,
                        'image_url' => $homePage->location_image_url,
                        'image_alt_text' => $homePage->location_image_alt_text,
                        'link_text' => $homePage->location_link_text,
                        'link_url' => $homePage->location_link_url,
                    ],
                    'featured_units' => $featuredUnits->map(function ($featuredUnit) {
                        return [
                            'id' => $featuredUnit->id,
                            'unit' => [
                                'id' => $featuredUnit->unit->id,
                                'name' => $featuredUnit->unit->name,
                                'slug' => $featuredUnit->unit->slug,
                                'short_description' => $featuredUnit->unit->short_description,
                                'main_image_url' => $featuredUnit->unit->main_image_url,
                                'bedrooms' => $featuredUnit->unit->bedrooms,
                                'bathrooms' => $featuredUnit->unit->bathrooms,
                                'carports' => $featuredUnit->unit->carports,
                                'land_area' => $featuredUnit->unit->land_area,
                                'building_area' => $featuredUnit->unit->building_area,
                                'project' => [
                                    'id' => $featuredUnit->unit->project->id,
                                    'name' => $featuredUnit->unit->project->name,
                                    'slug' => $featuredUnit->unit->project->slug,
                                ]
                            ]
                        ];
                    }),
                    'news' => [
                        'title' => $latestNewsFirst->title,
                        'slug' => $latestNewsFirst->slug,
                        'excerpt' => $latestNewsFirst->excerpt,
                        'image_url' => $latestNewsFirst->image_url,
                        'author' => $latestNewsFirst->author,
                        'published_at' => $latestNewsFirst->published_at->format('Y-m-d H:i:s'),
                        'published_at_formatted' => $latestNewsFirst->published_at->format('d M Y'),
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data home page',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get accessibility items only
     */
    /**
     * Get accessibility page content
     */
    public function getAccessibilityPage()
    {
        try {
            $accessibilityPage = AccessibilityPage::with(['bannerImages' => function($query) {
                $query->ordered();
            }])->first();
            
            $accessibilities = Accessibility::active()->ordered()->get();

            if (!$accessibilityPage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Accessibility tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Accessibility page berhasil diambil',
                'data' => [
                    'page' => [
                        'id' => $accessibilityPage->id,
                        'title' => $accessibilityPage->title,
                        'description' => $accessibilityPage->description,
                        'meta_title' => $accessibilityPage->meta_title,
                        'meta_description' => $accessibilityPage->meta_description,
                        'meta_keywords' => $accessibilityPage->meta_keywords,
                        'is_active' => $accessibilityPage->is_active,
                    ],
                    'banner_images' => $accessibilityPage->bannerImages->map(function ($banner) {
                        return [
                            'id' => $banner->id,
                            'image_url' => $banner->image_url,
                            'alt_text' => $banner->alt_text,
                            'caption' => $banner->caption,
                            'order' => $banner->order,
                        ];
                    }),
                    'accessibilities' => $accessibilities->map(function ($accessibility) {
                        return [
                            'id' => $accessibility->id,
                            'name' => $accessibility->name,
                            'description' => $accessibility->description,
                            'image_url' => $accessibility->image_url,
                            'order' => $accessibility->order,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data accessibility page',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get concept page content
     */
    public function getConceptPage()
    {
        try {
            $conceptPage = ConceptPage::first();
            $sections = ConceptPageSection::active()->ordered()->get();

            if (!$conceptPage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Concept page tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Concept page berhasil diambil',
                'data' => [
                    'page' => [
                        'title' => $conceptPage->title,
                        'description' => $conceptPage->description,
                        'banner_image_url' => $conceptPage->banner_image_url,
                        'banner_alt_text' => $conceptPage->banner_alt_text,
                        'meta_title' => $conceptPage->meta_title,
                        'meta_description' => $conceptPage->meta_description,
                        'meta_keywords' => $conceptPage->meta_keywords,
                    ],
                    'sections' => $sections->map(function ($section) {
                        return [
                            'id' => $section->id,
                            'title' => $section->title,
                            'content' => $section->content,
                            'image_url' => $section->image_url,
                            'image_alt_text' => $section->image_alt_text,
                            'layout_type' => $section->layout_type,
                            'layout_text' => $section->layout_text,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data concept page',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get all project categories
     */
    public function getProjectCategories()
    {
        try {
            $categories = ProjectCategory::active()->ordered()->withCount('projects')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori project berhasil diambil',
                'data' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'projects_count' => $category->projects_count,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data kategori project',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get all projects or filter by category
     */
    public function getProjects(Request $request)
    {
        try {
            $query = Project::active()->with(['category'])->withCount('units');

            // Filter by category if provided
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('category_slug')) {
                $category = ProjectCategory::where('slug', $request->category_slug)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            $projects = $query->ordered()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Projects berhasil diambil',
                'data' => $projects->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'slug' => $project->slug,
                        'short_description' => $project->short_description,
                        'main_image_url' => $project->main_image_url,
                        'banner_type' => $project->banner_type, // Tambah ini
                        'banner_url' => $project->banner_url,
                        'banner_video_url' => $project->banner_video_url, // Tambah ini
                        'banner_media_url' => $project->banner_media_url, // Tambah ini
                        'logo_url' => $project->logo_url,
                        'units_count' => $project->units_count,
                        'category' => [
                            'id' => $project->category->id,
                            'name' => $project->category->name,
                            'slug' => $project->category->slug,
                        ]
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data projects',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get project detail by slug
     */
    public function getProjectDetail($slug)
    {
        try {
            $project = Project::active()
                ->with(['category', 'images', 'facilityImages', 'units' => function($query) {
                    $query->active()->ordered();
                }])
                ->where('slug', $slug)
                ->first();

            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Project tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Project detail berhasil diambil',
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'slug' => $project->slug,
                    'short_description' => $project->short_description,
                    'description' => $project->description,
                    'meta_title' => $project->meta_title,
                    'meta_description' => $project->meta_description,
                    'meta_keywords' => $project->meta_keywords,
                    'main_image_url' => $project->main_image_url,
                    'banner_type' => $project->banner_type, // Tambah ini
                    'banner_media_url' => $project->banner_media_url, // Tambah ini
                    'logo_url' => $project->logo_url,
                    'siteplan_image_url' => $project->siteplan_image_url,
                    'category' => [
                        'id' => $project->category->id,
                        'name' => $project->category->name,
                        'slug' => $project->category->slug,
                    ],
                    'gallery_images' => $project->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'caption' => $image->caption,
                        ];
                    }),
                    'facility_images' => $project->facilityImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'title' => $image->title,
                            'description' => $image->description,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                        ];
                    }),
                    'units' => $project->units->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'name' => $unit->name,
                            'slug' => $unit->slug,
                            'short_description' => $unit->short_description,
                            'main_image_url' => $unit->main_image_url,
                            'bedrooms' => $unit->bedrooms,
                            'bathrooms' => $unit->bathrooms,
                            'carports' => $unit->carports,
                            'land_area' => $unit->land_area,
                            'building_area' => $unit->building_area,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data project detail',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get unit detail by project slug and unit slug
     */
    public function getUnitDetail($projectSlug, $unitSlug)
    {
        try {
            $project = Project::active()->where('slug', $projectSlug)->first();
            
            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Project tidak ditemukan',
                    'data' => null
                ], 404);
            }

            $unit = Unit::active()
                ->with(['project', 'galleries', 'specifications'])
                ->where('project_id', $project->id)
                ->where('slug', $unitSlug)
                ->first();

            if (!$unit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unit tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Unit detail berhasil diambil',
                'data' => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'slug' => $unit->slug,
                    'short_description' => $unit->short_description,
                    'description' => $unit->description,
                    'meta_title' => $unit->meta_title,
                    'meta_description' => $unit->meta_description,
                    'meta_keywords' => $unit->meta_keywords,
                    'bedrooms' => $unit->bedrooms,
                    'bathrooms' => $unit->bathrooms,
                    'carports' => $unit->carports,
                    'land_area' => $unit->land_area,
                    'building_area' => $unit->building_area,
                    'main_image_url' => $unit->main_image_url,
                    'banner_url' => $unit->banner_url,
                    'floor_plan_image_url' => $unit->floor_plan_image_url,
                    'project' => [
                        'id' => $unit->project->id,
                        'name' => $unit->project->name,
                        'slug' => $unit->project->slug,
                    ],
                    'gallery_images' => $unit->galleries->map(function ($gallery) {
                        return [
                            'id' => $gallery->id,
                            'image_url' => $gallery->image_url,
                            'alt_text' => $gallery->alt_text,
                            'caption' => $gallery->caption,
                        ];
                    }),
                    'specifications' => $unit->specifications->map(function ($spec) {
                        return [
                            'id' => $spec->id,
                            'name' => $spec->name,
                            'value' => $spec->value,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data unit detail',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get published news
     */
    public function getNews(Request $request)
    {
        try {
            $query = News::published();

            // Pagination
            $perPage = $request->get('per_page', 10);
            $news = $query->orderBy('published_at', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'News berhasil diambil',
                'data' => [
                    'current_page' => $news->currentPage(),
                    // 'data' => $news->items(),
                    'first_page_url' => $news->url(1),
                    'from' => $news->firstItem(),
                    'last_page' => $news->lastPage(),
                    'last_page_url' => $news->url($news->lastPage()),
                    'next_page_url' => $news->nextPageUrl(),
                    'path' => $news->path(),
                    'per_page' => $news->perPage(),
                    'prev_page_url' => $news->previousPageUrl(),
                    'to' => $news->lastItem(),
                    'total' => $news->total(),
                    'news' => collect($news->items())->map(function ($article) {
                        return [
                            'id' => $article->id,
                            'title' => $article->title,
                            'slug' => $article->slug,
                            'excerpt' => $article->excerpt,
                            'image_url' => $article->image_url,
                            'author' => $article->author,
                            'published_at' => $article->published_at->format('Y-m-d H:i:s'),
                            'published_at_formatted' => $article->published_at->format('d M Y'),
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data news',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get news detail by slug
     */
    public function getNewsDetail($slug)
    {
        try {
            $news = News::published()->where('slug', $slug)->first();

            if (!$news) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'News tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Get related news (same category or recent)
            $relatedNews = News::published()
                ->where('id', '!=', $news->id)
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'News detail berhasil diambil',
                'data' => [
                    'news' => [
                        'id' => $news->id,
                        'title' => $news->title,
                        'slug' => $news->slug,
                        'content' => $news->content,
                        'meta_title' => $news->meta_title,
                        'meta_description' => $news->meta_description,
                        'meta_keywords' => $news->meta_keywords,
                        'excerpt' => Str::limit(strip_tags($news->content), 100), // Limit to 100 characters
                        'image_url' => $news->image_url,
                        'author' => $news->author,
                        'published_at' => $news->published_at->format('Y-m-d H:i:s'),
                        'published_at_formatted' => $news->published_at->format('d M Y'),
                    ],
                    'related_news' => $relatedNews->map(function ($article) {
                        return [
                            'id' => $article->id,
                            'title' => $article->title,
                            'excerpt' => $article->excerpt,
                            'slug' => $article->slug,
                            'image_url' => $article->image_url,
                            'published_at_formatted' => $article->published_at->format('d M Y'),
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data news detail',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get Accessibilities
     */
    public function getAccessibilities()
    {
        try {
            $accessibilities = Accessibility::active()->ordered()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Accessibilities berhasil diambil',
                'data' => $accessibilities->map(function ($accessibility) {
                    return [
                        'id' => $accessibility->id,
                        'name' => $accessibility->name,
                        'description' => $accessibility->description,
                        'image_url' => $accessibility->image_url,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data Accessibilities',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get FAQs grouped by category
     */
    public function getFaqs()
    {
        try {
            $faqs = Faqs::active()->ordered()->get();
            $groupedFaqs = $faqs->groupBy('category');

            return response()->json([
                'status' => 'success',
                'message' => 'FAQs berhasil diambil',
                'data' => [
                    'categories' => $groupedFaqs->keys(),
                    'faqs_by_category' => $groupedFaqs->map(function ($categoryFaqs) {
                        return $categoryFaqs->map(function ($faq) {
                            return [
                                'id' => $faq->id,
                                'question' => $faq->question,
                                'answer' => $faq->answer,
                                'category' => $faq->category,
                            ];
                        });
                    }),
                    'all_faqs' => $faqs->map(function ($faq) {
                        return [
                            'id' => $faq->id,
                            'question' => $faq->question,
                            'answer' => $faq->answer,
                            'category' => $faq->category,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data FAQs',
                'data' => null
            ], 500);
        }
    }

    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        // Rate limiting to prevent spam
        $key = 'contact-api:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'status' => 'error',
                'message' => "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.",
                'data' => null
            ], 429);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'to' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000|min:10',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 2 karakter',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
            'to.max' => 'Pilihan departemen tidak valid',
            'message.required' => 'Pesan wajib diisi',
            'message.min' => 'Pesan minimal 10 karakter',
            'message.max' => 'Pesan maksimal 1000 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        try {
            // Sanitize input data
            $data = [
                'name' => strip_tags(trim($request->name)),
                'email' => strtolower(trim($request->email)),
                'phone' => $this->formatPhoneNumber($request->phone),
                'to' => $request->to ?: null,
                'message' => strip_tags(trim($request->message)),
                'status' => 'unread'
            ];

            // Create contact message
            ContactMessage::create($data);

            // Hit rate limiter
            RateLimiter::hit($key, 300); // 5 minutes decay

            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih atas pesan Anda! Kami akan segera menghubungi Anda.',
                'data' => null
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form API submission failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Maaf, terjadi kesalahan saat mengirim pesan. Silakan coba lagi.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Format phone number to Indonesian standard
     */
    private function formatPhoneNumber($phone)
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Handle Indonesian phone number formatting
        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62') && !str_starts_with($phone, '+62')) {
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+') && strlen($phone) >= 10) {
            $phone = '+62' . $phone;
        }

        return $phone;
    }
}