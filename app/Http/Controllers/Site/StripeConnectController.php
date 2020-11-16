<?php

namespace App\Http\Controllers\Site;

use Stripe\OAuth;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Exception\OAuth\InvalidGrantException;

class StripeConnectController extends Controller
{
    public function index(Tenant $tenant)
    {
        $uuid = Str::uuid()->toString();
        cache()->set($uuid, $tenant, now()->addHours(24));

        return redirect()->away(sprintf(
            'https://connect.stripe.com/oauth/authorize?client_id=%s&state=%s&scope=read_write&response_type=code&stripe_user[email]=%s',
            config('services.stripe.client_id'),
            $uuid,
            $tenant->email,
        ));
    }

    public function store(Request $request)
    {
        $cacheKey = $request->get('state');
        $stripeOauthToken = $request->get('code');

        if (!$stripeOauthToken || !$cacheKey || !cache()->has($cacheKey)) {
            abort(422);
        }

        $tenant = cache()->get($cacheKey);

        try {
            $stripeResponse = resolve(OAuth::class)->token([
                'grant_type' => 'authorization_code',
                'code' => $stripeOauthToken,
            ]);

            $tenant->update([
                'stripe_connect_id' => $stripeResponse->stripe_user_id,
                'active' => true,
            ]);

            return redirect()
                ->tenant_route($tenant->domains()->first()->domain, 'app.frontend')
                ->withSuccess('Stripe payment gateway connected!');
        } catch (InvalidGrantException $e) {
            Log::error($e->getMessage());
        }

        return redirect()
                ->tenant_route($tenant->domains()->first()->domain, 'app.frontend')
                ->withError('There was an error linking your stripe account, please login to continue');
    }
}
