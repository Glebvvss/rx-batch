<?php

declare(strict_types=1);

namespace RxBatch;

use Rx\Observable;

class Batch
{
    private array $observables;

    private function __construct(array $observables)
    {
        $this->observables = $observables;
    }

    public static function of(array $observables): Observable
    {
        return (new self($observables))->execute();
    }

    private function execute(): Observable
    {
        return Observable::forkJoin(
            $this->getPreparedObservables(),
            fn(...$payloads) => $this->reduce($payloads)
        );
    }

    private function getPreparedObservables(): array
    {
        return array_map(
            fn($key) => $this->observables[$key]->map(fn($data) => [$key, $data]),
            array_keys($this->observables)
        );
    }

    private function reduce(array $payloads): array
    {
        return array_reduce($payloads, function($result, $payload) {
            [$key, $data] = $payload;
            $result[$key] = $data;
            return $result;
        }, []);
    }
}