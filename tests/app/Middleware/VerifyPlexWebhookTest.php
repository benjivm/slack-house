<?php

namespace Test\App\Middleware;

use App\Middleware\VerifyPlexWebhook;
use Slim\Http\Request;
use Slim\Http\Response;
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

    public function testReturnReponseWhenHookDisabled()
    {
        $config = [
            'plex' => ['webhooks' => 'disabled'],
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->build();

        $response = $verifyPlexWebhook($request, new Response, null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testRequestSchemaValidation()
    {
        $config = [
            'plex' => ['webhooks' => 'enabled'],
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->build();

        $response = $verifyPlexWebhook($request, new Response, null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $response->getStatusCode());        
    }
}
