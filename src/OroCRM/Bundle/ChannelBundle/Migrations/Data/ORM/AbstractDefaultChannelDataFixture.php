<?php

namespace OroCRM\Bundle\ChannelBundle\Migrations\Data\ORM;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Oro\Bundle\BatchBundle\ORM\Query\QueryCountCalculator;

use OroCRM\Bundle\ChannelBundle\Entity\Channel;

abstract class AbstractDefaultChannelDataFixture extends AbstractFixture implements
    ContainerAwareInterface,
    DependentFixtureInterface
{
    const BATCH_SIZE = 50;

    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManager */
    protected $em;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em        = $container->get('doctrine')->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return ['Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData'];
    }

    /**
     * @param string $entity
     *
     * @return int
     */
    protected function getRowCount($entity)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->getRepository($entity)
            ->createQueryBuilder('e');

        return QueryCountCalculator::calculateCount($qb->getQuery());
    }

    /**
     * @param Channel $channel
     * @param string  $entity
     * @param array   $additionalParameters
     */
    protected function fillChannelToEntity(Channel $channel, $entity, $additionalParameters = [])
    {
        $interfaces = class_implements($entity) ?: [];
        if (!in_array('OroCRM\\Bundle\\ChannelBundle\\Model\\ChannelAwareInterface', $interfaces)) {
            return;
        }

        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder()
            ->update($entity, 'e')
            ->set('e.dataChannel', $channel->getId())
            ->where('e.dataChannel IS NULL');
        if (!empty($additionalParameters)) {
            foreach ($additionalParameters as $parameterName => $value) {
                $qb->andWhere(
                    sprintf(
                        'e.%s = :%s',
                        $parameterName,
                        $parameterName
                    )
                )->setParameter($parameterName, $value);
            }
        }
        $qb->getQuery()
            ->execute();
    }
}
