<?php


namespace Pixelant\PxaSiteimprove\Controller;


use Pixelant\PxaSiteimprove\Utility\CompatibilityUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AjaxBackendController
{
    /**
     * @var array $responseArray
     */
    protected $responseArray = [];

    /**
     * Sets $responseArray['pageLink'] to the public URL of the current page
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function getPageLinkAction(ServerRequestInterface $request)
    {
        $pageId = (int)$request->getQueryParams()['id'];

        if (!$pageId) {
            $this->responseArray['pageLink'] = '';
            return $this->prepareJsonResponse();
        }

        $this->responseArray['pageLink'] = CompatibilityUtility::getPageUrl($pageId);

        return $this->prepareJsonResponse();
    }

    /**
     * @return Response
     */
    protected function prepareJsonResponse()
    {
        $response = GeneralUtility::makeInstance(Response::class);

        $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($this->responseArray));

        return $response;
    }
}
