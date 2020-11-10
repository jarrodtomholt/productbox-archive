<?php

namespace App\Http\Controllers\Site;

use App\Models\Tenant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\SignupRequest;

class SiteController extends Controller
{
    public function store(SignupRequest $request)
    {
        $tenant = Tenant::create($request->validated());

        $tenant->domains()->create([
            'domain' => sprintf('%s.%s', $tenant->slug, config('app.domain'))
        ]);

        $tenant->newSubscription('default', 'monthly')
            ->trialDays(config('subscriptions.trial_days', null))
            ->create($request->stripeToken, [
                'email' => $request->email,
                'description' => $request->name,
            ]);

        session()->flash('accountCreated', true);

        return back();
    }
}
