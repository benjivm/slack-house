<?php

namespace Test\Builders;

use App\Controllers\PlexController;
use App\Services\Lifx;
use PHPUnit\Framework\TestCase;

class PlexControllerBuilder extends TestCase
{
    private $lifx;

    /**
     * @throws \ReflectionException
     *
     * @return $this
     */
    public function withLifxStub()
    {
        $this->lifx = $this->getMockBuilder(Lifx::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this;
    }

    /**
     * @return PlexController
     */
    public function build()
    {
        return new PlexController($this->lifx);
    }
}
