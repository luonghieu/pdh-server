<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoiceController extends Controller
{
    public function code(Request $request)
    {
        $codes = str_split($request->code);
        return response()->view('voices.verification', [
            'codes' => $codes,
        ])->header('Content-Type', 'text/xml');
    }
}
