<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeLineController extends Controller
{
    public function index(Request $request)
    {
        return view('web.timelines.index');
    }

    public function show(Request $request)
    {
        return view('web.timelines.show');
    }

    public function create(Request $request)
    {
        return view('web.timelines.create');
    }
}
