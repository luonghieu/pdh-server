<?php

namespace App\Http\Controllers\Admin\Cast;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function ratings(Request $request, User $user)
    {
        $ratings = $user->ratings()->with('order', 'user')->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.casts.rating', compact('ratings', 'user'));
    }
}
