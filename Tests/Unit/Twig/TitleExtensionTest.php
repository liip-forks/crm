<?php

namespace Oro\Bundle\NavigationBundle\Tests\Unit\Twig;

use Oro\Bundle\NavigationBundle\Twig\TitleExtension;

class TitleExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $service;

    /**
     * @var TitleExtension
     */
    private $extension;

    public function setUp()
    {
        $this->service = $this->getMock('Oro\Bundle\NavigationBundle\Provider\TitleServiceInterface');
        $this->extension = new TitleExtension($this->service);
    }

    public function testFunctionDeclaration()
    {
        $functions = $this->extension->getFunctions();
        $this->assertArrayHasKey('oro_title_render', $functions);
        $this->assertArrayHasKey('oro_title_render_stored', $functions);
        $this->assertArrayHasKey('oro_title_render_serialized', $functions);
    }

    public function testNameConfigured()
    {
        $this->assertTrue(is_string($this->extension->getName()));
    }

    public function testRenderSerialized()
    {
        $this->service->expects($this->once())
                      ->method('getSerialized');

        $this->extension->renderSerialized();
    }

    public function testRenderStored()
    {
        $data = array();

        $this->service->expects($this->once())
            ->method('renderStored')
            ->with($this->equalTo($data));

        $this->extension->renderStored($data);
    }

    public function testSet()
    {
        $data = array();

        $this->service->expects($this->once())
            ->method('setData')
            ->with($this->equalTo($data));

        $this->extension->set($data);
    }

    public function testRender()
    {
        $this->service->expects($this->once())
            ->method('render');

        $this->extension->render();
    }

    public function testTokenParserDeclarations()
    {
        $result = $this->extension->getTokenParsers();

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
    }
}
