<?php

namespace Test\Builders;

use App\Controllers\PlexController;
use App\Services\Lifx\Lifx;
use PHPUnit\Framework\TestCase;

class PlexControllerBuilder extends TestCase
{
    private $lifx;

    public function withLifxStub()
    {
        $this->lifx = $this->getMockBuilder(Lifx::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this;
    }

    public function build()
    {
        return new PlexController($this->lifx);
    }
}
