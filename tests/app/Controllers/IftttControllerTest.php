<?php

namespace Test\App\Controllers;

use App\Controllers\IftttController;
use PHPUnit\Framework\TestCase;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Builders\IftttControllerBuilder;

class IftttControllerTest extends TestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new IftttControllerBuilder();

        $this->iftttController = $this->builder->withAppCommandStub()
            ->withLifxStub()
            ->withIfttt()
            ->build();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function defineRequestEventCommand(string $event, string $command)
    {
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn((object)[
                'command' => $command,
                'event' => $event,
            ]);
    }

    public function testIntanciation()
    {
        $this->assertInstanceOf(IftttController::class, $this->iftttController);
    }

    public function testCommandNotSpecifiedReturnsReponse()
    {
        $this->defineRequestEventCommand('', '');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testAppCommandEnablePlexWebHooks()
    {
        $this->defineRequestEventCommand('app_command', 'enable_plex_webhooks');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAppCommandDisablePlexWebHooks()
    {
        $this->defineRequestEventCommand('app_command', 'disable_plex_webhooks');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHommeCommandActiveMovieTime()
    {
        $this->defineRequestEventCommand('home_command', 'activate_movie_time');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHommeCommandActiveBedTime()
    {
        $this->defineRequestEventCommand('home_command', 'activate_bed_time');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
