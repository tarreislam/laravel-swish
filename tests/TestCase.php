<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function fakeNumber()
    {
        $nmbr = '';
        for ($i = 0; $i < 8; $i++) {
            $nmbr .= mt_rand(0, 9);
        }
        return '46' . $nmbr;
    }
}