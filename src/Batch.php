<?php

namespace RxBatch;

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
                ->flatMap(function($resource) {
                    if (!$resource instanceof Observable) {
                        throw new InvalidArgumentException('Resource value must be string or Rx\Observable');
                    }

                    return $resource->map(fn($data) => $this->prepare($data));
                })
                ->subscribe(
                    fn(array $data) => $this->fill($data),
                    fn($e) => $reject($e),
                    fn() => $this->isDone() && $resolve($this->resultSet)
                );
        });
    
        return Observable::fromPromise($promise);
    }

    private function prepare($data): array
    {
        return [$this->getNextKey(), $data];
    }

    private function getNextKey(): string
    {
        $keys = array_keys($this->resources);
        $key = $keys[$this->pointer];
        ++$this->pointer;
        return $key;
    }

    private function isDone(): bool
    {
        return count($this->resultSet) === count($this->resources);
    }

    private function fill(array $data): void
    {
        [$resource, $result] = $data;
        $this->resultSet[$resource] = $result;
    }
}