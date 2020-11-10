<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Laravel\Sanctum\Sanctum;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider addressValidationProvider
     */
    public function it_validates_email_and_password_fields($field, $value)
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('auth.address'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function addressValidationProvider()
    {
        return [
            ['address', ''],
            ['city', ''],
            ['state', ''],
            ['state', 'BlahBlahBlah'],
            ['postcode', ''],
            ['postcode', 'BLAH'],
            ['postcode', '123456'],
        ];
    }

    /** @test */
    public function a_users_addresses_are_returned_on_login()
    {
        $user = User::factory()->create();
        $user->addresses()->create(Address::factory()->make()->toArray());

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertSee($user->fullname)
            ->assertSee($user->email)
            ->assertSee($user->phone);

        $user->addresses->each(function ($address) use ($response) {
            $response->assertSee($address->address)
                ->assertSee($address->city)
                ->assertSee($address->state)
                ->assertSee($address->postcode);
        });
    }

    /** @test */
    public function only_an_authenticated_user_can_add_addresses()
    {
        $address = Address::factory()->make()->toArray();

        $this->postJson(route('auth.address'), $address)->assertStatus(401);
    }

    /** @test */
    public function a_users_can_add_addresses()
    {
        $user = User::factory()->create();
        $address = Address::factory()->make();

        Sanctum::actingAs($user);

        $this->postJson(route('auth.address'), $address->toArray())->assertSuccessful()
            ->assertSee($address->address)
            ->assertSee($address->city)
            ->assertSee($address->state)
            ->assertSee($address->postcode);

        $this->assertTrue($user->fresh()->addresses->count() === 1);
    }

    /** @test */
    public function a_users_can_remove_addresses()
    {
        $user = User::factory()->create();
        $user->addresses()->create(Address::factory()->make()->toArray());

        $address = $user->addresses()->first();

        Sanctum::actingAs($user);

        $this->deleteJson(route('auth.address'), [
            'address' => Hashids::encode($address->id),
        ])->assertSuccessful()
        ->assertDontSee($address->address)
        ->assertDontSee($address->city)
        ->assertDontSee($address->state)
        ->assertDontSee($address->postcode);

        $this->assertTrue($user->fresh()->addresses->count() === 0);
    }
}
