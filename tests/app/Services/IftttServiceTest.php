<?php

namespace App\Tests\App\Services;

use App\Services\Ifttt;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class IftttServiceTest extends TestCase
{
    /**

     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    /**
     * @test
     */
    public function theIftttServiceIsInstantiated()
    {
        $ifttt = new Ifttt([], $this->client);

        $this->assertInstanceOf(Ifttt::class, $ifttt);
    }
}
