<?php

namespace Test\App\Services\Lifx;

use GuzzleHttp\Client;
use App\Services\Lifx\Lifx;
use PHPUnit\Framework\TestCase;

class VerifyPlexWebhookTest extends TestCase
{
    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    public function testInstantiation()
    {
        $lifx = new Lifx([], $this->client);

        $this->assertInstanceOf(Lifx::class, $lifx);
    }
}
