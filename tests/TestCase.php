<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $basePath = \Illuminate\Foundation\Application::inferBasePath();
        $bootstrapFile = file_exists($basePath.'/bootstrap/app.php')
            ? $basePath.'/bootstrap/app.php'
            : $basePath.'/bootstrap/bootstrap.php';

        $app = require $bootstrapFile;

        $this->traitsUsedByTest = class_uses_recursive(static::class);

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
