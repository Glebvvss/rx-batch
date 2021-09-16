# Rx Batch

Library for processing miltiple observables as single batch uses RxPHP.

### Use Case
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rx\Observable;
use Rx\React\Http;
use RxBatch\Batch;

$resources = [
    'task_1' => Http::get('https://www.google.com'),
    'task_2' => Http::get('https://www.google.com'),
    'task_3' => Observable::of('Result 3'),
];

Batch::of($resources)->subscribe(
    function($data) {
        print_r($data['task_1']);
        print_r($data['task_2']);
        print_r($data['task_3']);
    },
    function($e) {
        echo $e->getMessage();
    },
);
```