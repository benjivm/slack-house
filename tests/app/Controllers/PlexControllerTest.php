<?php

use App\Controllers\PlexController;
use PHPUnit\Framework\TestCase;
use Slim\Http\Request;
use Slim\Http\Response;
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
            ->willReturn((object)[
                'event' => $event,
            ]);
    }

    public function testInstanciation()
    {
        $this->assertInstanceOf(PlexController::class, $this->plexController);
    }

    public function testMediaPlay()
    {
        $this->defineRequestEvent('media.play');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMediaPause()
    {
        $this->defineRequestEvent('media.pause');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMediaResume()
    {
        $this->defineRequestEvent('media.resume');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMediaStop()
    {
        $this->defineRequestEvent('media.stop');

        $response = $this->plexController->__invoke($this->request, new Response());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
