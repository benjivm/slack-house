<?php

namespace Test\App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use PHPUnit\Framework\TestCase;
use App\Controllers\IftttController;
use Test\Builders\IftttControllerBuilder;

class IftttControllerTest extends TestCase
{
    protected $builder;

    public function setUp(): void
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
            ->willReturn((object) [
                'command' => $command,
                'event' => $event,
            ]);
    }

    /**
     * @test
     */
    public function theIftttControllerIsInstantiated()
    {
        $this->assertInstanceOf(IftttController::class, $this->iftttController);
    }

    /**
     * @test
     */
    public function emptyCommandsFail()
    {
        $this->defineRequestEventCommand('', '');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function appCommandEnablePlexWebhooksSucceeds()
    {
        $this->defineRequestEventCommand('app_command', 'enable_plex_webhooks');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function appCommandDisablePlexWebhooksSucceeds()
    {
        $this->defineRequestEventCommand('app_command', 'disable_plex_webhooks');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function homeCommandActivateMovieTimeSucceeds()
    {
        $this->defineRequestEventCommand('home_command', 'activate_movie_time');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function homeCommandActivateBedTimeSucceeds()
    {
        $this->defineRequestEventCommand('home_command', 'activate_bed_time');

        $response = $this->iftttController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
