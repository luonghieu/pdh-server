<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        return view('web.point.index', compact('user'));
    }
}
