<?php

namespace OroCRM\Bundle\CampaignBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use OroCRM\Bundle\CampaignBundle\Provider\EmailTransportProvider;

class EmailTransportSelectType extends AbstractType
{
    /**
     * @var EmailTransportProvider
     */
    protected $emailTransportProvider;

    /**
     * @param EmailTransportProvider $emailTransportProvider
     */
    public function __construct(EmailTransportProvider $emailTransportProvider)
    {
        $this->emailTransportProvider = $emailTransportProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'choices' => $this->getChoices()
            ]
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $transports = $this->emailTransportProvider->getTransports();
        $choices = array();
        foreach ($transports as $transport) {
            $choices[$transport->getName()] = $transport->getLabel();
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_campaign_email_transport_select';
    }
}
