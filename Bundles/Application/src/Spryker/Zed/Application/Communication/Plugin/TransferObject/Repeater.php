<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Application\Communication\Plugin\TransferObject;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Library\Config;
use Spryker\Shared\Library\Log;
use Spryker\Shared\ZedRequest\Client\RequestInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Spryker\Zed\Application\Business\ApplicationFacade;
use Spryker\Zed\Application\Communication\ApplicationCommunicationFactory;

/**
 * @method ApplicationFacade getFacade()
 * @method ApplicationCommunicationFactory getFactory()
 */
class Repeater extends AbstractPlugin
{

    /**
     * @var bool
     */
    protected $isRepeatInProgress = false;

    /**
     * @param string|null $mvc
     *
     * @return string
     */
    public function getRepeatData($mvc = null)
    {
        $this->isRepeatInProgress = true;
        if ($mvc !== null) {
            return Log::getFlashInFile('last_yves_request_' . $mvc . '.log');
        } else {
            return Log::getFlashInFile('last_yves_request.log');
        }
    }

    /**
     * @param RequestInterface $transferObject
     * @param HttpRequest $httpRequest
     *
     * @return void
     */
    public function setRepeatData(RequestInterface $transferObject, HttpRequest $httpRequest)
    {
        if ($this->isRepeatInProgress) {
            return;
        }

        if (Config::get(ApplicationConstants::SET_REPEAT_DATA, false) === false) {
            return;
        }

        $repeatData = [
            'module' => $httpRequest->attributes->get('module'),
            'controller' => $httpRequest->attributes->get('controller'),
            'action' => $httpRequest->attributes->get('action'),
            'params' => $transferObject->toArray(false),
        ];

        $mvc = sprintf(
            '%s_%s_%s',
            $httpRequest->attributes->get('module'),
            $httpRequest->attributes->get('controller'),
            $httpRequest->attributes->get('action')
        );

        Log::setFlashInFile($repeatData, 'last_yves_request_' . $mvc . '.log');
        Log::setFlashInFile($repeatData, 'last_yves_request.log');
    }

}
