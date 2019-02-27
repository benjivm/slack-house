<?php

namespace Test\App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use PHPUnit\Framework\TestCase;
use App\Middleware\VerifyIftttWebhook;
use Test\Builders\VerifyIftttWebhookBuilder;

class VerifyIftttWebhookTest extends TestCase
{
    public function setUp(): void
    {
        $this->builder = new VerifyIftttWebhookBuilder();
    }

    /**
     * @test
     */
    public function theVerifyIftttWebhookClassIsInstantiated()
    {
        $verifyIftttWebhook = $this->builder->withConfig(['ifttt' => []])
            ->withMonologStub()
            ->withValidatorStub()
            ->build();

        $this->assertInstanceOf(VerifyIftttWebhook::class, $verifyIftttWebhook);
    }

    /**
     * @test
     */
    public function invalidPayloadsDoNotPassValidation()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $verifyIftttWebhook = $this->builder->withConfig(['ifttt' => []])
            ->withMonologStub()
            ->withValidatorMock()
            ->build();

        $response = $verifyIftttWebhook($request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function invalidMakerKeysFail()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['key' => 'test']);

        $config = [
            'ifttt' => ['key' => 'key'],
        ];

        $verifyIftttWebhook = $this->builder->withConfig($config)
            ->withMonologStub()
            ->withValidatorStub()
            ->withValidationPassed()
            ->build();

        $response = $verifyIftttWebhook($request, new Response(), null);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
