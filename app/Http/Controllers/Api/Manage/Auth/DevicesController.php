<?php

namespace App\Http\Controllers\Api\Manage\Auth;

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DevicesController extends Controller
{
    public function store(Request $request, Agent $agent)
    {
        if (!$request->token) {
            return response()->noContent();
        }

        auth()->user()->devices()->updateOrCreate([
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
