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

    public function setUp(): void
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
    public function thePlexControllerIsInstantiated()
    {
        $this->assertInstanceOf(PlexController::class, $this->plexController);
    }

    /**
     * @test
     */
    public function eventMediaPlaySucceeds()
    {
        $this->defineRequestEvent('media.play');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function eventMediaPauseSucceeds()
    {
        $this->defineRequestEvent('media.pause');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function eventMediaResumeSucceeds()
    {
        $this->defineRequestEvent('media.resume');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function eventMediaStopSucceeds()
    {
        $this->defineRequestEvent('media.stop');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
