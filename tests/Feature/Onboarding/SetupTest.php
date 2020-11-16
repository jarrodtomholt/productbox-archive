<?php

namespace Tests\Feature\Onboarding;

use Tests\TestCase;
use App\Models\Tenant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_valid_setup_signature_is_required_to_finalise_setup()
    {
        $tenant = Tenant::factory()->create();

        $this->get(route('setup', $tenant))->assertStatus(403);

        $this->assertFalse(session()->has("{$tenant->slug}.setup.verified"));

        $this->get(URL::temporarySignedRoute('setup', now()->addMinutes(2), $tenant))->assertSuccessful();

        $this->assertTrue(session()->has("{$tenant->slug}.setup.verified"));
    }

    /** @test */
    public function it_redirects_if_tenant_is_alreay_setup()
    {
        $tenant = Tenant::factory()->active()->create();

        $this->get(URL::temporarySignedRoute('setup', now()->addMinutes(10), $tenant))->assertRedirect();

        $this->assertFalse(session()->has("{$tenant->slug}.setup.verified"));
    }

    /**
     * @test
     * @dataProvider setupValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $tenant = Tenant::factory()->create();

        session()->put("{$tenant->slug}.setup.verified", true);

        $response = $this->postJson(URL::temporarySignedRoute('setup', now()->addMinutes(10), $tenant), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function setupValidationProvider()
    {
        return [
            ['logo', ''],
            ['logo', 'NOT A VALID IMAGE.gif'],
            ['abn', ''],
            ['abn', rand(10000000000, 99999999999)],
            ['state', ''],
            ['state', 'NOT VALID'],
            ['name', ''],
            ['email', ''],
            ['email', 'not an email'],
            ['password', ''],
            ['password', 'not a valid password'],
            ['password_confirmation', ''],
            ['openingHours', ''],
            ['openingHours', 'asdfasdfasdf'],
            ['openingHours', ['asdf' => '1234']],
        ];
    }

    /** @test */
    public function it_uploads_the_account_logo_creates_an_admin_and_sets_settings_and_openingHours()
    {
        $tenant = Tenant::factory()->create();

        session()->put("{$tenant->slug}.setup.verified", true);

        $response = $this->postJson(URL::temporarySignedRoute('setup', now()->addMinutes(10), $tenant), [
            'logo' => UploadedFile::fake()->image('fake_logo.png'),
            'abn' => 34118972998,
            'state' => 'VIC',
            'name' => 'Fake Contact',
            'email' => 'fakeemail@' . config('app.domain'),
            'password' => 'ThisIsASuperSecureP$ssw0Rd',
            'password_confirmation' => 'ThisIsASuperSecureP$ssw0Rd',
            'openingHours' => [
                'monday' => '10:00-20:00',
            ],
        ])->assertRedirect();

        $this->assertFileExists(sprintf(
            '%s/logos/%s',
            config('filesystems.disks.uploads.root'),
            $tenant->slug . '.png'
        ));

        tenancy()->initialize($tenant->fresh());

        $this->assertDatabaseHas('admins', [
            'name' => 'Fake Contact',
            'email' => 'fakeemail@' . config('app.domain'),
        ]);
    }
}
