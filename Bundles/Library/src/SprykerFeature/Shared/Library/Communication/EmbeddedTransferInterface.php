<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Shared\Library\Communication;

use SprykerEngine\Shared\Transfer\TransferInterface;

interface EmbeddedTransferInterface
{

    /**
     * @param TransferInterface $transferObject
     *
     * @return $this
     */
    public function setTransfer(TransferInterface $transferObject);

    /**
     * @return TransferInterface
     */
    public function getTransfer();

}
