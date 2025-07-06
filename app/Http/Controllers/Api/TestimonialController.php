<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index()
    {
        return Testimonial::where('is_approved', true)->get();
    }

    public function featured()
    {
        return Testimonial::where('is_approved', true)->where('is_featured', true)->get();
    }

    public function store(StoreTestimonialRequest $request)
    {
        $testimonial = Testimonial::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'rating' => $request->rating,
            'is_approved' => false
        ]);

        return response()->json($testimonial, 201);
    }

    public function userTestimonials(Request $request)
    {
        return $request->user()->testimonials;
    }

    // Admin methods
    public function adminIndex()
    {
        return Testimonial::paginate();
    }

    public function approve($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_approved' => true]);
        return response()->json(['message' => 'Testimonial approved']);
    }

    public function feature($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_featured' => true]);
        return response()->json(['message' => 'Testimonial featured']);
    }

    public function destroy($id)
    {
        Testimonial::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
