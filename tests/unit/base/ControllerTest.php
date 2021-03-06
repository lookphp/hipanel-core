<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\tests\unit\base;

use hipanel\base\Controller;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-04-27 at 13:36:04.
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Controller
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Controller('test', null);
    }

    protected function tearDown()
    {
    }

    public function testActions()
    {
        $this->assertInternalType('array', $this->object->actions());
    }

    public function testBehaviors()
    {
        $this->assertInternalType('array', $this->object->behaviors());
    }
}
