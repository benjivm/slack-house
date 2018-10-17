<?php

namespace App\Services\Ifttt;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class IftttServiceTest extends TestCase
{
    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    /**
     * @test
     */
    public function the_ifttt_service_is_instantiated()
    {
        $ifttt = new Ifttt([], $this->client);

        $this->assertInstanceOf(Ifttt::class, $ifttt);
    }
}
