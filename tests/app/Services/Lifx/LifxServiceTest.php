<?php

namespace Test\App\Services\Lifx;

use GuzzleHttp\Client;
use App\Services\Lifx\Lifx;
use PHPUnit\Framework\TestCase;

class LifxServiceTest extends TestCase
{
    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    /**
     * @test
     */
    public function the_lifx_service_is_instantiated()
    {
        $lifx = new Lifx([], $this->client);

        $this->assertInstanceOf(Lifx::class, $lifx);
    }
}
