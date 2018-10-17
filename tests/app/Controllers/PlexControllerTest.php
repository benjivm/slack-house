<?php

namespace Test\App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use PHPUnit\Framework\TestCase;
use App\Controllers\PlexController;
use Test\Builders\PlexControllerBuilder;

class PlexControllerTest extends TestCase
{
    private $request;

    private $builder;

    public function setUp()
    {
        $this->builder = new PlexControllerBuilder();

        $this->plexController = $this->builder
            ->withLifxStub()
            ->build();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function defineRequestEvent(string $event)
    {
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn((object) [
                'event' => $event,
            ]);
    }

    /**
     * @test
     */
    public function the_plex_controller_is_instantiated()
    {
        $this->assertInstanceOf(PlexController::class, $this->plexController);
    }

    /**
     * @test
     */
    public function event_media_play_succeeds()
    {
        $this->defineRequestEvent('media.play');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function event_media_pause_succeeds()
    {
        $this->defineRequestEvent('media.pause');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function event_media_resume_succeeds()
    {
        $this->defineRequestEvent('media.resume');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function event_media_stop_succeeds()
    {
        $this->defineRequestEvent('media.stop');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
