<?php

namespace Tests;

use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Foundation\Testing\TestResponse;

trait AddsCustomAssertions
{
    protected function setupTestResponseMacros()
    {
        TestResponse::macro('assertResponseHas', function ($key, $value) {
            $data = $this->original->getData();
            $data = $data['page']['props'] ?? $data;

            PHPUnit::assertEquals($data[$key], $value);

            return $this;
        });

        TestResponse::macro('assertResponseMissing', function ($key, $value) {
            $data = $this->original->getData();
            $data = $data['page']['props'] ?? $data;

            PHPUnit::assertNotEquals($data[$key], $value);

            return $this;
        });

        TestResponse::macro('assertViewContains', function ($key, $value) {
            $this->data($key)->assertContains($value);

            return $this;
        });

        TestResponse::macro('assertViewNotContains', function ($key, $value) {
            $this->data($key)->assertNotContains($value);

            return $this;
        });

        TestResponse::macro('assertViewData', function ($callback) {
            PHPUnit::assertTrue($callback((object) $this->original->getData()));

            return $this;
        });
    }
}