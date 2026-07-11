<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $salon = request()->attributes->get('salon');
        $query = Feedback::with(['user', 'booking'])
            ->where('is_published', true);

        if ($salon) {
            $query->whereHas('booking.service', function ($q) use ($salon) {
                $q->where('user_id', $salon->id);
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('about', compact('reviews'));
    }
}
