<?php

namespace App\Http\Controllers;

use App\Card;
use Auth;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $card = $user->card;

        if ($request->session()->has('backUrl')) {
            $request->session()->forget('backUrl');
        }

        if ($card) {
            $request->session()->push('backUrl', \URL::previous());
            return view('web.cards.index', compact('card'));
        } else {
            return view('web.cards.create');
        }
    }

    public function update()
    {
        $user = Auth::user();
        $card = $user->card;

        return view('web.cards.edit', compact('card'));
    }
}
