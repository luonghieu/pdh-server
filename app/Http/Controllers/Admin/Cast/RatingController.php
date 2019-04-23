<?php

namespace App\Http\Controllers\Admin\Cast;

use App\Http\Controllers\Controller;
use App\User;
use App\Rating;
use App\Services\LogService;
use DB;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function ratings(Request $request, User $user)
    {
        $ratings = $user->ratings()->with('order', 'user')->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.casts.rating', compact('ratings', 'user'));
    }

    public function detail(User $user, Rating $rating)
    {
        return view('admin.casts.rating_detail', compact('rating'));
    }

    public function update(User $user, Rating $rating, Request $request)
    {
        try {
            $rules = [
                'memo' => 'required|max:350',
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            DB::beginTransaction();

            $rating->memo = $request->memo;
            $rating->is_valid = $request->is_valid;
            $rating->save();

            // Update rating_score for cast
            $avgScore = $user->ratings()->where('is_valid', true)->avg('score');
            $user->rating_score = round($avgScore, 1);
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);

            return back();
        }

        return redirect()->route('admin.casts.guest_rating_detail', compact('user', 'rating'));
    }
}
