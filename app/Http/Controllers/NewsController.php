<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('content', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Order by published_at and created_at
        $news = $query->orderBy('published_at', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->paginate(10)
                     ->appends($request->query());
        
        return view('pages.news.index', compact('news'));
    }

    public function create()
    {
        return view('pages.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:news,title',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published',
        ], [
            'title.required' => 'Title is required',
            'title.unique' => 'Title already exists',
            'title.max' => 'Title cannot exceed 255 characters',
            'content.required' => 'Content is required',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'author.max' => 'Author name cannot exceed 255 characters',
            'published_at.date' => 'Published date must be a valid date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            // Generate slug
            $slug = GeneratorService::generateSlug(new News(), $request->title);

            // Upload image if provided
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = ImageService::uploadAndCompress(
                    $request->file('image'), 
                    'news', 
                    85, 
                    1200
                );
            }

            // Set published_at
            $publishedAt = null;
            if ($request->status === 'published') {
                $publishedAt = $request->published_at ? $request->published_at : now();
            }

            News::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'image_path' => $imagePath,
                'author' => $request->author,
                'status' => $request->status,
                'published_at' => $publishedAt,
            ]);

            return redirect()->route('news.index')
                           ->with('success', 'News created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create news: ' . $e->getMessage());
        }
    }

    public function edit(News $news)
    {
        return view('pages.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:news,title,' . $news->id,
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published',
        ], [
            'title.required' => 'Title is required',
            'title.unique' => 'Title already exists',
            'title.max' => 'Title cannot exceed 255 characters',
            'content.required' => 'Content is required',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'author.max' => 'Author name cannot exceed 255 characters',
            'published_at.date' => 'Published date must be a valid date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            // Generate slug if title changed
            $slug = GeneratorService::generateSlug(new News(), $request->title, $news->id);

            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $news->image_path,
                'news',
                85,
                1200
            );

            // Handle published_at
            $publishedAt = $news->published_at;
            if ($request->status === 'published') {
                if (!$publishedAt || $request->published_at) {
                    $publishedAt = $request->published_at ? $request->published_at : now();
                }
            } else {
                $publishedAt = null;
            }

            $news->update([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'image_path' => $imagePath,
                'author' => $request->author,
                'status' => $request->status,
                'published_at' => $publishedAt,
            ]);

            return redirect()->back()
                           ->with('success', 'News updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update news: ' . $e->getMessage());
        }
    }

    public function destroy(News $news)
    {
        try {
            // Delete image if exists
            if ($news->image_path) {
                ImageService::deleteFile($news->image_path);
            }

            $news->delete();

            return redirect()->route('news.index')
                           ->with('success', 'News deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('news.index')
                           ->with('error', 'Failed to delete news: ' . $e->getMessage());
        }
    }
}