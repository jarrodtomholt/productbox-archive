<?php

namespace App\Http\Controllers\Api\Auth;

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DevicesController extends Controller
{
    public function store(Request $request, Agent $agent)
    {
        if (!$request->token || auth()->check() === false) {
            return response()->noContent();
        }

        auth()->user()->devices()->updateOrCreate([
            'user_id' => auth()->user()->id,
            'device_id' => $request->deviceId,
        ], [
            'token' => $request->token,
            'platform' => $agent->platform(),
            'version' => $agent->version($agent->platform()),
            'app_version' => $request->appVersion,
            'ip' => $request->ip(),
        ]);

        return response()->noContent();
    }
}
