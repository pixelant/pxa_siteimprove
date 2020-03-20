<?php


namespace Pixelant\PxaSiteimprove\Controller;


use Pixelant\PxaSiteimprove\Utility\CompatibilityUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class EidController
{
    /**
     * @var array $responseArray
     */
    protected $responseArray = [];

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * Main request handler
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function processRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;

        switch (isset($request->getQueryParams()['action']) ? (string)$request->getQueryParams()['action'] : '') {
            case 'pageLink':
                $this->pageLinkAction();
                break;
            default:
                throw new \UnexpectedValueException('Unknown or no action defined.', 1584698015);
        }

        $this->prepareResponse($response);

        return $response;
    }

    /**
     * Sets $responseArray['pageLink'] to the public URL of the current page
     *
     * @return void
     */
    protected function pageLinkAction()
    {
        $pageId = (int)$this->request->getQueryParams()['id'];

        if (!$pageId) {
            $this->responseArray['pageLink'] = '';
            return;
        }

        $this->responseArray['pageLink'] = CompatibilityUtility::getPageUrl($pageId);
    }

    /**
     * @param ResponseInterface $response
     * @return void
     */
    protected function prepareResponse(ResponseInterface &$response)
    {
        $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($this->responseArray));
    }
}
