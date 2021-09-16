<?php

namespace Tests\Batch;

use Mockery;
use Rx\Scheduler;
use Rx\Observable;
use Rx\React\Http;
use RxBatch\Batch;
use React\Promise\Promise;
use React\EventLoop\Factory;
use PHPUnit\Framework\TestCase;

class BatchTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $loop = Factory::create();
        Scheduler::setDefaultFactory(fn() => new Scheduler\ImmediateScheduler($loop));
        $loop->run();
    }

    public function testUsesHttpClients(): void
    {
        $dub = Mockery::mock('alias:' . Http::class)->allows();
        $dub->get('https://example1.com')
            ->andReturn(Observable::of('Response 1'));

        $dub->get('https://example2.com')
            ->andReturn(Observable::of('Response 2'));

        $resources = [
            'request_1' => Http::get('https://example1.com'),
            'request_2' => Http::get('https://example2.com'),
        ];

        Batch::of($resources)->subscribe(function($data) {
            $this->assertEquals(
                [
                    'request_1' => 'Response 1',
                    'request_2' => 'Response 2',
                ], 
                $data
            );
        });
    }

    public function testUsesRegularObservables(): void
    {
        $resources = [
            'task_1' => Observable::of('Result 1'),
            'task_2' => Observable::of('Result 2'),
        ];

        Batch::of($resources)->subscribe(function($data) {
            $this->assertEquals(
                [
                    'task_1' => 'Result 1',
                    'task_2' => 'Result 2',
                ], 
                $data
            );
        });
    }

    public function testUsesNoAssocArray(): void
    {
        $resources = [
            Observable::of('Result 1'),
            Observable::of('Result 2'),
        ];

        Batch::of($resources)->subscribe(function($data) {
            $this->assertEquals(
                ['Result 1', 'Result 2',], 
                $data
            );
        });
    }
}