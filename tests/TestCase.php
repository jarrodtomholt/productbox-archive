<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $tenancy = false;

    protected $tenant = null;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->tenant = Tenant::factory()->active()->create();
            tenancy()->initialize($this->tenant);
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // delete generated tenant databases
        foreach (glob(database_path('productbox_*')) as $database) {
            unlink($database);
        }
    }
}
