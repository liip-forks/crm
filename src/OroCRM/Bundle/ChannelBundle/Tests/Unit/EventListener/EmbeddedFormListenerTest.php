<?php

namespace OroCRM\Bundle\ChannelBundle\Tests\Unit\EventListener;

use Oro\Bundle\EmbeddedFormBundle\Event\EmbeddedFormSubmitBeforeEvent;

use OroCRM\Bundle\ChannelBundle\EventListener\EmbeddedFormListener;

use Symfony\Component\HttpFoundation\Request;

class EmbeddedFormListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request */
    protected $request;

    protected function setUp()
    {
        $this->request = new Request([], [], ['_route'=>'oro_embedded_form_']);
    }

    public function testAddDataChannelField()
    {
        $env      = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $newField = "<input>";

        $env->expects($this->once())
            ->method('render')
            ->will($this->returnValue($newField));

        $formView        = $this->getMockBuilder('Symfony\Component\Form\FormView')
            ->disableOriginalConstructor()
            ->getMock();
        $currentFormData = 'someHTML';
        $formData        = [
            'dataBlocks' => [
                [
                    'subblocks' => [
                        ['data' => [$currentFormData]]
                    ]
                ]
            ]
        ];

        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeFormRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getTwigEnvironment')
            ->will($this->returnValue($env));
        $event->expects($this->once())
            ->method('getFormData')
            ->will($this->returnValue($formData));
        $event->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($formView));

        array_unshift($formData['dataBlocks'][0]['subblocks'][0]['data'], $newField);
        $event->expects($this->once())
            ->method('setFormData')
            ->with($formData);

        $listener = new EmbeddedFormListener($this->request);
        $listener->addDataChannelField($event);
    }

    public function testOnEmbeddedFormSubmit()
    {
        $formEntity = $this->getMockBuilder('OroCRM\Bundle\ChannelBundle\Tests\Unit\Stubs\Entity\EmbeddedFormStub')
            ->disableOriginalConstructor()->getMock();
        $formEntity->expects($this->never())
            ->method('getDataChannel');
        $event = new EmbeddedFormSubmitBeforeEvent([], $formEntity);

        $listener = new EmbeddedFormListener($this->request);
        $listener->onEmbeddedFormSubmit($event);
    }

    public function testOnEmbeddedFormSubmitWithDataChannel()
    {
        $formEntity = $this->getMockBuilder('OroCRM\Bundle\ChannelBundle\Tests\Unit\Stubs\Entity\EmbeddedFormStub')
            ->disableOriginalConstructor()->getMock();

        $dataChannel = $this->getMock('OroCRM\Bundle\ChannelBundle\Entity\Channel');
        $formEntity->expects($this->once())
            ->method('getDataChannel')
            ->will($this->returnValue($dataChannel));
        $data = $this->getMock('OroCRM\Bundle\ChannelBundle\Model\ChannelAwareInterface');
        $data->expects($this->once())
            ->method('setDataChannel');
        $event = new EmbeddedFormSubmitBeforeEvent($data, $formEntity);

        $listener = new EmbeddedFormListener($this->request);
        $listener->onEmbeddedFormSubmit($event);
    }
}
