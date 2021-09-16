# Rx Batch

Library for processing miltiple observables as single batch uses RxPHP.

### Use Case
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rx\React\Http;
use RxBatch\Batch;

$resources = [
    'request_1' => Http::get('https://www.google.com'),
    'request_2' => Http::get('https://www.google.com'),
    'request_3' => Http::get('https://www.google.com'),
];

Batch::of($resources)->subscribe(function($data) {
    print_r($data['request_1']);
    print_r($data['request_2']);
    print_r($data['request_3']);
});
```