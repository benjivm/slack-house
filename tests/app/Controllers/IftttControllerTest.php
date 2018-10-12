<?php

namespace Test\App\Controllers;

use App\Controllers\IftttController;
use PHPUnit\Framework\TestCase;
use Test\Builders\IftttControllerBuilder;

class IftttControllerTest extends TestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new IftttControllerBuilder();
    }

    public function testIntanciation()
    {
        $iftttController = $this->builder->withAppCommandStub()
            ->withLifxStub()
            ->build();

        $this->assertInstanceOf(IftttController::class, $iftttController);
    }
}
