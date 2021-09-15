<?php

namespace Tests\UnionHttp;

use Mockery;
use Rx\Scheduler;
use Rx\Observable;
use Rx\React\Http;
use React\Promise\Promise;
use RxUnionHttp\UnionHttp;
use React\EventLoop\Factory;
use PHPUnit\Framework\TestCase;

class UnionHttpTest extends TestCase
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

        UnionHttp::of($resources)->subscribe(function($data) {
            $this->assertEquals(
                [
                    'request_1' => 'Response 1',
                    'request_2' => 'Response 2',
                ], 
                $data
            );
        });
    }

    public function testUsesRawLinks(): void
    {
        $dub = Mockery::mock('alias:' . Http::class)->allows();
        $dub->get('https://example1.com')
            ->andReturn(Observable::of('Response 1'));

        $dub->get('https://example2.com')
            ->andReturn(Observable::of('Response 2'));

        $resources = [
            'request_1' => 'https://example1.com',
            'request_2' => 'https://example2.com',
        ];

        UnionHttp::of($resources)->subscribe(function($data) {
            $this->assertEquals(
                [
                    'request_1' => 'Response 1',
                    'request_2' => 'Response 2',
                ], 
                $data
            );
        });
    }
}