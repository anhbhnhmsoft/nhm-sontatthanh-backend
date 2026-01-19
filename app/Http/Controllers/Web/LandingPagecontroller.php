<?php

namespace App\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPagecontroller extends Controller
{
    public function download(Request $request)
    {
        $agent = $request->userAgent();
        if (Str::contains($agent, 'android') && config('app.download_link_android') !== null) {
            return redirect()->away(config('app.download_link_android'));
        }
        if (Str::contains($agent, 'ios') && config('app.download_link_ios') !== null) {
            return redirect()->away(config('app.download_link_ios'));
        }
        return redirect()->away(config('app.download_link_ios'));
    }
}
