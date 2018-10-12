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

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstanciation()
    {
        $verifyPlexWebhook = $this->builder->withConfig(['plex' => []])
            ->withMonologStub()
            ->withValidatorStub()
            ->build();
            
        $this->assertInstanceOf(VerifyPlexWebhook::class, $verifyPlexWebhook);
    }

    public function testReturnReponseWhenHookDisabled()
    {
        $config = [
            'plex' => ['webhooks' => 'disabled'],
        ];

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->build();

        $response = $verifyPlexWebhook($this->request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testRequestSchemaValidation()
    {
        $config = [
            'plex' => ['webhooks' => 'enabled'],
        ];

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorMock()
            ->build();

        $response = $verifyPlexWebhook($this->request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testErrorResponsePlayerIsNotAllowed()
    {
        $config = [
            'plex' => [
                'webhooks' => 'enabled',
                'players' => ['error'],
                'allowedMedia' => ['test'],
            ],
        ];

        $payload = [
            'payload' => json_encode([
                'Player' => [
                    'uuid' => 'test',
                ],
                'Metadata' => [
                    'librarySectionType' => 'test',
                ]
            ]),
        ];

        $this->request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn($payload);

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->withValidatonPassed()
            ->build();

        $response = $verifyPlexWebhook($this->request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testErrorResponseTypeIsNotAllowed()
    {
        $config = [
            'plex' => [
                'webhooks' => 'enabled',
                'players' => ['test'],
                'allowedMedia' => ['error'],
            ],
        ];

        $payload = [
            'payload' => json_encode([
                'Player' => [
                    'uuid' => 'test',
                ],
                'Metadata' => [
                    'librarySectionType' => 'test',
                ]
            ]),
        ];

        $this->request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn($payload);

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->withValidatonPassed()
            ->build();

        $response = $verifyPlexWebhook($this->request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
