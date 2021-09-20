<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Rx\Observable;
use Rx\React\Http;
use RxBatch\Batch;

$resources = [
    'task_1' => Observable::of('Result 1')->delay(500),
    'task_2' => Observable::of('Result 2')->delay(250),
    'task_3' => Observable::of('Result 3')->delay(125),
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