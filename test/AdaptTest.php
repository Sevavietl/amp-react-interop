<?php
namespace Interop\Test;

use Amp\Failure;
use Amp\Loop;
use Amp\Success;
use React\Promise\FulfilledPromise;
use React\Promise\RejectedPromise;

class AdaptTest extends \PHPUnit\Framework\TestCase {

    public function testAmpFulfilled() {
        $value = 1;

        Loop::run(function () use (&$result, $value) {
            $react = new FulfilledPromise($value);

            $promise = \Interop\Amp\Promise\adapt($react);

            $promise->onResolve(function ($error = null, $inValue = null) use (&$result) {
                $result = $inValue;
            });
        });

        $this->assertSame($value, $result);
    }

    public function testAmpRejected() {
        $value = new \Exception("test");

        Loop::run(function () use (&$result, $value) {
            $react = new RejectedPromise($value);

            $promise = \Interop\Amp\Promise\adapt($react);

            $promise->onResolve(function ($error = null, $inValue = null) use (&$result) {
                $result = $error;
            });
        });

        $this->assertSame($value, $result);
    }

    public function testReactFulfilled() {
        $value = 1;

        Loop::run(function () use (&$result, $value) {
            $amp = new Success($value);

            $promise = \Interop\React\Promise\adapt($amp);

            $promise->then(function ($inValue) use (&$result) {
                $result = $inValue;
            });
        });

        $this->assertSame($value, $result);
    }

    public function testReactRejected() {
        $exception = new \Exception;

        Loop::run(function () use (&$result, $exception) {
            $amp = new Failure($exception);

            $promise = \Interop\React\Promise\adapt($amp);

            $promise->then(function ($inValue) {
                //do nothing
            }, function ($inValue) use (&$result) {
                $result = $inValue;
            });
        });

        $this->assertSame($exception, $result);
    }
}
