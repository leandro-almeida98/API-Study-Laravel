<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Forçar uso do MySQL
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'task_manager_api_test']);
    }
}
