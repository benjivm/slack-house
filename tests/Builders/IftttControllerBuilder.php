<?php

namespace Test\Builders;

use App\Controllers\IftttController;
use App\Interfaces\AppCommandInterface;
use App\Services\Ifttt;
use App\Services\Lifx;
use PHPUnit\Framework\TestCase;

class IftttControllerBuilder extends TestCase
{
    private $appCommand;

    private $lifx;

    private $ifttt;

    /**
     * @throws \ReflectionException
     *
     * @return $this
     */
    public function withAppCommandStub()
    {
        $this->appCommand = $this->getMockBuilder(AppCommandInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this;
    }

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
     * @throws \ReflectionException
     *
     * @return $this
     */
    public function withIfttt()
    {
        $this->ifttt = $this->getMockBuilder(Ifttt::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this;
    }

    /**
     * @return IftttController
     */
    public function build()
    {
        return new IftttController($this->appCommand, $this->lifx, $this->ifttt);
    }
}
