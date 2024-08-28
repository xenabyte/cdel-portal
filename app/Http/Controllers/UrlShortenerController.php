<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use Illuminate\Support\Str;

class UrlShortenerController extends Controller
{
    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();

        return redirect($shortUrl->url);
    }
}

