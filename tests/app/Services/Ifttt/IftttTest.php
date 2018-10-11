<?php

namespace App\Services\Ifttt;

use GuzzleHttp\Client;
use App\Services\Ifttt\Ifttt;
use PHPUnit\Framework\TestCase;

class IftttTest extends TestCase
{
    private $config;

    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    public function testCreatingIftttServiceInstance()
    {
        $ifttt = new Ifttt([], $this->client);

        $this->assertInstanceOf(Ifttt::class, $ifttt);
    }
}
