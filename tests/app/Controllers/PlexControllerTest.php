<?php

namespace App\Tests\App\Controllers;

use App\Controllers\PlexController;
use App\Tests\Builders\PlexControllerBuilder;
use PHPUnit\Framework\TestCase;
use Slim\Http\Request;
use Slim\Http\Response;

class PlexControllerTest extends TestCase
{
    private $request;

    private $builder;

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @param string $event
     */
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
