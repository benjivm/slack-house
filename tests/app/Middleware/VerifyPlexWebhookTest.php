<?php

namespace Test\App\Middleware;

use App\Middleware\VerifyPlexWebhook;
use Test\Builders\VerifyPlexWebhookBuilder;
use PHPUnit\Framework\TestCase;

class VerifyPlexWebhookTest extends TestCase
{
    public function setUp()
    {
        $this->builder = new VerifyPlexWebhookBuilder();
    }

    public function testInstanciation()
    {
        $verifyPlexWebhook = $this->builder->withConfig(['plex' => []])
            ->withMonologStub()
            ->build();
            
        $this->assertInstanceOf(VerifyPlexWebhook::class, $verifyPlexWebhook);
    }
}
