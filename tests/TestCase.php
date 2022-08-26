<?php declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as TestCaseBase;

class TestCase extends TestCaseBase
{
    protected function getPackageProviders($app)
    {
        return [];
    }
}
