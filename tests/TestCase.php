<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $provides = [
        \Livewire\LivewireManager::class,
        \Livewire\Volt\VoltManager::class,
    ];
}
