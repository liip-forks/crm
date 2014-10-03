<?php

namespace OroCRM\Bundle\ChannelBundle\Tests\Selenium\Sales;

use Oro\Bundle\TestFrameworkBundle\Test\Selenium2TestCase;
use OroCRM\Bundle\ChannelBundle\Tests\Selenium\Pages\Channels;

/**
 * Class ChannelTest
 *
 * @package OroCRM\Bundle\ChannelBundle\Tests\Selenium\Sales
 */
class ChannelTest extends Selenium2TestCase
{
    /**
     * @return string
     */
    public function testCreateChannel()
    {
        $name = 'Channel_' . mt_rand();

        $login = $this->login();
        /** @var Channels $login */
        $login->openChannels('OroCRM\Bundle\ChannelBundle')
            ->assertTitle('Channels - System')
            ->add()
            ->assertTitle('Create Channel - Channels - System')
            ->setName($name)
            ->setStatus('Active')
            ->setType('Custom')
            ->setStatus('Active')
            ->addEntity('Opportunity')
            ->addEntity('Lead')
            ->addEntity('Sales Process')
            ->addEntity('B2B customer')
            ->save()
            ->assertMessage('Channel saved');

        return $name;
    }
}