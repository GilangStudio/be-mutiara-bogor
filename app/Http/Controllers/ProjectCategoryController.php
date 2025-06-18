<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectCategory;
use App\Services\GeneratorService;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        $categories = ProjectCategory::ordered()->get();
        return view('pages.development-category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name',
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique' => 'Nama kategori sudah ada',
            'name.max' => 'Nama kategori maksimal 255 karakter'
        ]);

        try {
            $slug = GeneratorService::generateSlug(new ProjectCategory(), $request->name);
            $order = GeneratorService::generateOrder(new ProjectCategory());

            ProjectCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'order' => $order,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('development.category.index')
                           ->with('success', 'Kategori berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->route('development.category.index')
                           ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ProjectCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name,' . $category->id,
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique' => 'Nama kategori sudah ada',
            'name.max' => 'Nama kategori maksimal 255 karakter'
        ]);

        try {
            $slug = GeneratorService::generateSlug(new ProjectCategory(), $request->name, $category->id);

            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('development.category.index')
                           ->with('success', 'Kategori berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->route('development.category.index')
                           ->with('error', 'Gagal mengupdate kategori: ' . $e->getMessage());
        }
    }

    public function destroy(ProjectCategory $category)
    {
        try {
            $category->delete();
            
            // Reorder setelah delete
            GeneratorService::reorderAfterDelete(new ProjectCategory());

            return redirect()->route('development.category.index')
                           ->with('success', 'Kategori berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->route('development.category.index')
                           ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:project_categories,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                ProjectCategory::where('id', $orderData['id'])
                             ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}