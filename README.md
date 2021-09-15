# Rx Union Http

Library for processing parallel http requests as single batch.

### Use Case
```
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rx\React\Http;
use RxUnionHttp\UnionHttp;

$resources = [
    'request_1' => 'https://www.google.com',
    'request_2' => 'https://www.google.com',
    'request_3' => Http::get('https://www.google.com'),
];

UnionHttp::of($resources)->subscribe(function($data) {
    print_r($data['request_1']);
    print_r($data['request_2']);
    print_r($data['request_3']);
});
```