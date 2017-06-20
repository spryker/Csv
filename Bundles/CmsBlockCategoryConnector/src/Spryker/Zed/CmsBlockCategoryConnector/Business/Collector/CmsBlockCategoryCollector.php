<?php

namespace Spryker\Zed\CmsBlockCategoryConnector\Business\Collector;

use Spryker\Shared\CmsBlock\CmsBlockCategoryConnectorConstants;
use Spryker\Zed\CmsBlockCategoryConnector\Persistence\Collector\Storage\Propel\CmsBlockCategoryConnectorCollector;
use Spryker\Zed\Collector\Business\Collector\Storage\AbstractStoragePropelCollector;

class CmsBlockCategoryCollector extends AbstractStoragePropelCollector
{
    /**
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem($touchKey, array $collectItemData)
    {
        return $this->extractCmsBlockNames($collectItemData[CmsBlockCategoryConnectorCollector::COL_CMS_BLOCK_NAMES]);
    }

    /**
     * @return string
     */
    protected function collectResourceType()
    {
        return CmsBlockCategoryConnectorConstants::RESOURCE_TYPE_CMS_BLOCK_CATEGORY_CONNECTOR;
    }

    /**
     * @param string $cmsBlockNames
     *
     * @return array
     */
    protected function extractCmsBlockNames($cmsBlockNames)
    {
        $separator = ',';
        return explode($separator, trim($cmsBlockNames));
    }

    /**
     * @return bool
     */
    protected function isStorageTableJoinWithLocaleEnabled()
    {
        return true;
    }

}