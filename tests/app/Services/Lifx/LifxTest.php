<?php

namespace Test\App\Services\Lifx;

use App\Services\Lifx\Lifx;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class LifxTest extends TestCase
{
    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    public function testInstanciation()
    {
        $lifx = new Lifx([], $this->client);

        $this->assertInstanceOf(Lifx::class, $lifx);
    }
}
