<?php

namespace App\Http\Controllers;

use App\Models\Faqs;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class FaqsController extends Controller
{
    public function index()
    {
        $faqs = Faqs::ordered()->get();
        return view('pages.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
        ], [
            'question.required' => 'Question is required',
            'question.max' => 'Question cannot exceed 255 characters',
            'answer.required' => 'Answer is required',
            'category.required' => 'Category is required',
            'category.max' => 'Category cannot exceed 100 characters'
        ]);

        try {
            $order = GeneratorService::generateOrder(new Faqs());

            Faqs::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'category' => $request->category,
                'order' => $order,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('faqs.index')
                           ->with('success', 'FAQ created successfully');

        } catch (\Exception $e) {
            return redirect()->route('faqs.index')
                           ->with('error', 'Failed to create FAQ: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Faqs $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
        ], [
            'question.required' => 'Question is required',
            'question.max' => 'Question cannot exceed 255 characters',
            'answer.required' => 'Answer is required',
            'category.required' => 'Category is required',
            'category.max' => 'Category cannot exceed 100 characters'
        ]);

        try {
            $faq->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'category' => $request->category,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('faqs.index')
                           ->with('success', 'FAQ updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('faqs.index')
                           ->with('error', 'Failed to update FAQ: ' . $e->getMessage());
        }
    }

    public function destroy(Faqs $faq)
    {
        try {
            $faq->delete();
            
            // Reorder setelah delete
            GeneratorService::reorderAfterDelete(new Faqs());

            return redirect()->route('faqs.index')
                           ->with('success', 'FAQ deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('faqs.index')
                           ->with('error', 'Failed to delete FAQ: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:faqs,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                Faqs::where('id', $orderData['id'])
                   ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}