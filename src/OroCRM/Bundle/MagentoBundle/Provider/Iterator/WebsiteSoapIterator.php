<?php

namespace OroCRM\Bundle\MagentoBundle\Provider\Iterator;

use OroCRM\Bundle\MagentoBundle\Provider\Transport\SoapTransport;

class WebsiteSoapIterator extends AbstractLoadeableSoapIterator
{
    /**
     * {@inheritdoc}
     */
    protected function getData()
    {
        $websites = [];
        $stores   = $this->transport->getStores();
        foreach ($stores as $store) {
            $websites[$store['website_id']]['name'][] = $store['name'];
            $websites[$store['website_id']]['code'][] = $store['code'];
        }

        foreach ($websites as $websiteId => $websiteItem) {
            $websites[$websiteId]['name'] = implode(SoapTransport::WEBSITE_NAME_SEPARATOR, $websiteItem['name']);
            $websites[$websiteId]['code'] = implode(SoapTransport::WEBSITE_CODE_SEPARATOR, $websiteItem['code']);
            $websites[$websiteId]['id']   = $websiteId;
        }

        return $websites;
    }
}
