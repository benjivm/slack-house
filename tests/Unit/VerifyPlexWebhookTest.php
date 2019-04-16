<?php

namespace App\Tests\Unit;

use App\Middleware\VerifyPlexWebhook;
use App\Tests\Builders\VerifyPlexWebhookBuilder;
use PHPUnit\Framework\TestCase;
use Slim\Http\Request;
use Slim\Http\Response;

class VerifyPlexWebhookTest extends TestCase
{
    /**

     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $this->builder = new VerifyPlexWebhookBuilder();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function theVerifyPlexWebhookClassIsInstantiated()
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
    public function plexCommandsFailWhileWebhooksAreDisabled()
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
    public function invalidPayloadsDoNotPassValidation()
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
    public function invalidPlayersFail()
    {
        $config = [
            'plex' => [
                'webhooks'      => 'enabled',
                'players'       => ['error'],
                'allowed_media' => ['test'],
            ],
        ];

        $payload = [
            'payload' => json_encode([
                'event'  => 'media.play',
                'Player' => [
                    'uuid' => 'test',
                ],
                'Metadata' => [
                    'librarySectionType' => 'test',
                    'title'              => 'test',
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
    public function invalidMediaTypesFail()
    {
        $config = [
            'plex' => [
                'webhooks'      => 'enabled',
                'players'       => ['test'],
                'allowed_media' => ['error'],
            ],
        ];

        $payload = [
            'payload' => json_encode([
                'event'  => 'media.play',
                'Player' => [
                    'uuid' => 'test',
                ],
                'Metadata' => [
                    'librarySectionType' => 'test',
                    'title'              => 'test',
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
