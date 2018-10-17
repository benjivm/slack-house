<?php

namespace Test\App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use PHPUnit\Framework\TestCase;
use App\Middleware\VerifyPlexWebhook;
use Test\Builders\VerifyPlexWebhookBuilder;

class VerifyPlexWebhookTest extends TestCase
{
    public function setUp()
    {
        $this->builder = new VerifyPlexWebhookBuilder();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function the_verify_plex_webhook_class_is_instantiated()
    {
        $verifyPlexWebhook = $this->builder->withConfig(['plex' => []])
            ->withMonologStub()
            ->withValidatorStub()
            ->build();
            
        $this->assertInstanceOf(VerifyPlexWebhook::class, $verifyPlexWebhook);
    }

    /**
     * @test
     */
    public function plex_commands_fail_while_webhooks_are_disabled()
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

    /**
     * @test
     */
    public function invalid_payloads_do_not_pass_validation()
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

    /**
     * @test
     */
    public function invalid_players_fail()
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
                ],
            ]),
        ];

        $this->request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn($payload);

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->withValidationPassed()
            ->build();

        $response = $verifyPlexWebhook($this->request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function invalid_media_types_fail()
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
                ],
            ]),
        ];

        $this->request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn($payload);

        $verifyPlexWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->withValidationPassed()
            ->build();

        $response = $verifyPlexWebhook($this->request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
