<?php

namespace Test\Builders;

use App\Services\Lifx;
use PHPUnit\Framework\TestCase;
use App\Controllers\PlexController;

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
