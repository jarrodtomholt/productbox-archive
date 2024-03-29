<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $tenancy = false;

    protected $tenant = null;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');

        if ($this->tenancy) {
            $this->tenant = Tenant::factory()->active()->create();
            tenancy()->initialize($this->tenant);
        }
    }

    public function tearDown(): void
    {
        // delete generated tenant databases
        foreach (glob(database_path('productbox_*')) as $database) {
            unlink($database);
        }

        // delete all tenant storage
        foreach (glob(base_path('storage/productbox_*')) as $storage) {
            exec(sprintf("rm -rf %s", escapeshellarg($storage)));
        }

        // delete tenant uploads
        if (tenant()) {
            exec(sprintf("rm -rf %s", escapeshellarg(base_path('public/uploads/' . md5(tenant('id'))))));
            exec(sprintf("rm -rf %s", escapeshellarg(base_path('public/uploads/logos/' . tenant('slug') . '.png'))));
        }

        /* HANDY - if mysql is set or used for some reason
            SELECT CONCAT('DROP DATABASE `', SCHEMA_NAME, '`;')
            FROM `information_schema`.`SCHEMATA`
            WHERE SCHEMA_NAME LIKE 'productbox_%';
        */

        tenancy()->end();

        parent::tearDown();
    }
}
