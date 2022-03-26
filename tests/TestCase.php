<?php

namespace Tests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Laravel catches any exception to display it in the laravel error screen.
     * When debugging tests this can be avoided calling this method up front
     * to display the exception instead of Failed asserting message.
     *
     * @return void
     */
    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct()
            {
            }

            public function report(\Throwable $e)
            {
                // no-op
            }

            public function render($request, \Throwable $e)
            {
                throw $e;
            }
        });
    }
}
