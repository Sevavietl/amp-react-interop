<?php

namespace Amp\Promise {
    use Amp\Deferred;
    use Amp\Promise;

    /**
     * Adapts any object with a done(callable $onFulfilled, callable $onRejected) or then(callable $onFulfilled,
     * callable $onRejected) method to a promise usable by components depending on placeholders implementing
     * \AsyncInterop\Promise.
     *
     * @param object $promise Object with a done() or then() method.
     *
     * @return \Amp\Promise Promise resolved by the $thenable object.
     *
     * @throws \Error If the provided object does not have a then() method.
     */
    function adapt($promise): Promise {
        $deferred = new Deferred;

        if (\method_exists($promise, 'done')) {
            $promise->done([$deferred, 'resolve'], [$deferred, 'fail']);
        } elseif (\method_exists($promise, 'then')) {
            $promise->then([$deferred, 'resolve'], [$deferred, 'fail']);
        } else {
            throw new \Error("Object must have a 'then' or 'done' method");
        }

        return $deferred->promise();
    }
}

namespace React\Promise {

    /**
     * @param \Amp\Promise $promise
     *
     * @return PromiseInterface
     */
    function adapt(\Amp\Promise $promise): PromiseInterface {
        $deferred = new Deferred();

        $promise->onResolve(function ($error = null, $result = null) use ($deferred) {
            if ($error) {
                $deferred->reject($error);
            } else {
                $deferred->resolve($result);
            }
        });

        return $deferred->promise();
    }
}