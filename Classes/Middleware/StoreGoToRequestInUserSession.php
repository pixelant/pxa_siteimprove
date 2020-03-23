<?php

namespace Pixelant\PxaSiteimprove\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class StoreGoToRequestInUserSession implements MiddlewareInterface
{
    /**
     *
     *
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->getBackendUser() !== null && $_REQUEST['tx_siteimprove_goto']) {
            $this->getBackendUser()->setAndSaveSessionData('tx_siteimprove_goto', $_REQUEST['tx_siteimprove_goto']);
        }

        return $handler->handle($request);
    }

    /**
     * Return BE user from global
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
