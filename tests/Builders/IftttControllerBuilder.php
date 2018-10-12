<?php

namespace Test\Builders;

use App\Controllers\IftttController;
use App\Interfaces\AppCommandInterface;
use App\Services\Ifttt\Ifttt;
use PHPUnit\Framework\TestCase;

class IftttControllerBuilder extends TestCase
{
    protected $appCommand;

    protected $lifx;

    public function withAppCommandStub()
    {
        $this->appCommand = $this->getMockBuilder(AppCommandInterface::class)
            ->getMock();

        return $this;
    }

    public function withLifxStub()
    {
        $this->lifx = $this->getMockBuilder(Ifttt::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this;
    }

    public function build()
    {
        return new IftttController($this->appCommand, $this->lifx);
    }
}
