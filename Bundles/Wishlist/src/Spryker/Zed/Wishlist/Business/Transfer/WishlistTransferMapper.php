<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Wishlist\Business\Transfer;

use Generated\Shared\Transfer\WishlistItemTransfer;
use Generated\Shared\Transfer\WishlistOverviewItemTransfer;
use Generated\Shared\Transfer\WishlistOverviewProductTransfer;
use Generated\Shared\Transfer\WishlistProductOverviewTransfer;
use Generated\Shared\Transfer\WishlistTransfer;
use Orm\Zed\Wishlist\Persistence\SpyWishlist;
use Orm\Zed\Wishlist\Persistence\SpyWishlistItem;
use Propel\Runtime\Collection\ObjectCollection;

class WishlistTransferMapper implements WishlistTransferMapperInterface
{

    /**
     * @param \Orm\Zed\Wishlist\Persistence\SpyWishlist $wishlistEntity
     *
     * @return \Generated\Shared\Transfer\WishlistTransfer
     */
    public function convertWishlist(SpyWishlist $wishlistEntity)
    {
        $wishlistTransfer = (new WishlistTransfer())
            ->fromArray($wishlistEntity->toArray(), true);

        return $wishlistTransfer;
    }

    /**
     * @param \Orm\Zed\Wishlist\Persistence\SpyWishlist[]|\Propel\Runtime\Collection\ObjectCollection $wishlistEntityCollection
     *
     * @return \Generated\Shared\Transfer\WishlistTransfer[]
     */
    public function convertWishlistCollection(ObjectCollection $wishlistEntityCollection)
    {
        $transferList = [];
        foreach ($wishlistEntityCollection as $wishlistEntity) {
            $transferList[] = $this->convertWishlist($wishlistEntity);
        }

        return $transferList;
    }

    /**
     * @param \Orm\Zed\Wishlist\Persistence\SpyWishlistItem $wishlistItemEntity
     *
     * @return \Generated\Shared\Transfer\WishlistItemTransfer
     */
    public function convertWishlistItem(SpyWishlistItem $wishlistItemEntity)
    {
        $wishlistItemTransfer = (new WishlistItemTransfer())
            ->fromArray($wishlistItemEntity->toArray(), true);

        return $wishlistItemTransfer;
    }

    /**
     * @param \Orm\Zed\Wishlist\Persistence\SpyWishlist[]|\Propel\Runtime\Collection\ObjectCollection $wishlistItemEntityCollection
     *
     * @return \Generated\Shared\Transfer\WishlistItemTransfer[]
     */
    public function convertWishlistItemCollection(ObjectCollection $wishlistItemEntityCollection)
    {
        $transferList = [];
        foreach ($wishlistItemEntityCollection as $wishlistEntity) {
            $transferList[] = $this->convertWishlistItem($wishlistEntity);
        }

        return $transferList;
    }

    /**
     * @param \Orm\Zed\Wishlist\Persistence\SpyWishlistItem $wishlistItemEntity
     *
     * @return \Generated\Shared\Transfer\WishlistOverviewProductTransfer
     */
    public function convertWishlistOverviewProduct(SpyWishlistItem $wishlistItemEntity)
    {
        $wishlistOverviewItemTransfer = (new WishlistOverviewProductTransfer())
            ->fromArray($wishlistItemEntity->toArray(), true);

        $wishlistOverviewItemTransfer
            ->setIdProduct($wishlistItemEntity->getSpyProduct()->getIdProduct())
            ->setSku($wishlistItemEntity->getSpyProduct()->getSku())
            ->setName($wishlistItemEntity->getSpyProduct()->getSpyProductLocalizedAttributess()->getFirst()->getName())
            ->setDescription($wishlistItemEntity->getSpyProduct()->getSpyProductLocalizedAttributess()->getFirst()->getDescription())
            ->setFkLocale($wishlistItemEntity->getSpyProduct()->getSpyProductLocalizedAttributess()->getFirst()->getFkLocale());

        return $wishlistOverviewItemTransfer;
    }

    /**
     * @param \Orm\Zed\Wishlist\Persistence\SpyWishlist[]|\Propel\Runtime\Collection\ObjectCollection $wishlistItemEntityCollection
     *
     * @return \Generated\Shared\Transfer\WishlistOverviewProductTransfer[]
     */
    public function convertWishlistOverviewProductCollection(ObjectCollection $wishlistItemEntityCollection)
    {
        $transferList = [];
        foreach ($wishlistItemEntityCollection as $wishlistEntity) {
            $transferList[] = $this->convertWishlistOverviewProduct($wishlistEntity);
        }

        return $transferList;
    }

}
