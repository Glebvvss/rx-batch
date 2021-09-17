<?php

declare(strict_types=1);

namespace RxBatch;

use Throwble;
use Rx\Observable;
use Rx\React\Http;
use React\Promise\Promise;
use InvalidArgumentException;

class Batch
{
    private int   $pointer   = 0;
    private array $resultSet = [];
    private array $resources;

    private function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    public static function of(array $resources): Observable
    {
        return (new self($resources))->execute();
    }

    private function execute(): Observable
    {
        $promise = new Promise(function($resolve, $reject) {
            Observable::fromArray($this->resources)
                ->flatMap(fn(Observable $observable) => Observable::of([
                    'key'        => $this->generateKey(),
                    'observable' => $observable
                ]))
                ->subscribe(
                    fn(array $payload) => $payload['observable']->subscribe(
                        fn($result) => $this->resultSet[$payload['key']] = $result,
                        fn($e)      => $reject($e),
                        fn()        => $this->isCompleted() && $resolve($this->resultSet)
                    ),
                    fn(Throwble $e) => $reject($e)
                );
        });

        return Observable::fromPromise($promise);
    }

    private function generateKey(): string
    {
        $keys = array_keys($this->resources);
        $key = $keys[$this->pointer];
        ++$this->pointer;
        return (string) $key;
    }

    private function isCompleted(): bool
    {
        return count($this->resultSet) === count($this->resources);
    }
}