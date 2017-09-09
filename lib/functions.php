<?php

namespace Interop\Amp\Promise {
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
    function adapt($promise): \Amp\Promise {
        return \Amp\Promise\adapt($promise);
    }
}

namespace Interop\React\Promise {
    /**
     * @param \Amp\Promise $promise
     *
     * @return \React\Promise\PromiseInterface
     */
    function adapt(\Amp\Promise $promise): \React\Promise\PromiseInterface {
        $deferred = new \React\Promise\Deferred();

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