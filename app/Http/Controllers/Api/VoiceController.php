<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoiceController extends Controller
{
    public function code(Request $request)
    {
        return response()->view('voices.verification', [
            'code' => $request->code,
        ])->header('Content-Type', 'text/xml');
    }
}
