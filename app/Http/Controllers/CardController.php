<?php

namespace App\Http\Controllers;

use App\Card;
use Auth;

class CardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $card = $user->card;

        if ($card) {
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
