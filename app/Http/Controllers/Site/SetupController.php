<?php

namespace App\Http\Controllers\Site;

use App\Models\Admin;
use App\Models\Tenant;
use App\Facades\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\SetupRequest;

class SetupController extends Controller
{
    public function index(Tenant $tenant, Request $request)
    {
        if ($tenant->active) {
            return redirect()->to(tenant_route($tenant->domains()->first()->domain, 'manage.main', ['any' => 'login']));
        }

        session()->put("{$tenant->slug}.setup.verified", $request->signature);

        return $tenant;
    }

    public function store(Tenant $tenant, SetupRequest $request)
    {
        $tenant->update([
            'abn' => $request->abn,
            'timezone' => config('support.timezones')[$request->state],
        ]);

        if ($request->hasFile('logo')) {
            $tenant->setLogo($request->file('logo'));
        }

        tenancy()->initialize($tenant);

        Settings::set([
            'openingHours' => collect($request->openingHours)->map(function ($item) {
                return [$item];
            })->toArray(),
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        tenancy()->end();

        return redirect()->to(tenant_route($tenant->domains()->first()->domain, 'manage.main', ['any' => 'login']));
    }
}
