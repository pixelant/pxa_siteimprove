<?php

namespace Pixelant\PxaSiteimprove\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageGenerator;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * Class PageUrlEidService
 * @package Pixelant\PxaSiteimprove\Service
 */
class PageUrlEidService
{
    /**
     * @var int
     */
    protected $pageId = 0;

    /**
     * Initializes variables for link
     */
    public function __construct()
    {
        $params = json_decode(base64_decode(GeneralUtility::_GP('data')), true);

        if (is_array($params)) {
            $this->pageId = (int)$params['id'];
        }

        EidUtility::initTCA();
    }

    /**
     * Generate link
     *
     * @return string
     * @throws \Exception
     */
    public function getLink()
    {
        if ($this->pageId) {
            $this->createTSFE();

            /** @var ContentObjectRenderer $cObj */
            $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

            $typoLinkConf = [
                'parameter' => $this->pageId,
                'forceAbsoluteUrl' => 1
            ];

            $url = $cObj->typoLink_URL($typoLinkConf) ?: '/';
            $parts = parse_url($url);

            return empty($parts['host']) ? GeneralUtility::locationHeaderUrl($url) : $url;
        }

        return '';
    }

    /**
     * Initializes TSFE. This is necessary to have proper environment for typoLink.
     *
     * @return void
     * @throws \Exception
     */
    protected function createTSFE()
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            $this->pageId,
            ''
        );
        $GLOBALS['TSFE'] = $tsfe;

        $tsfe->connectToDB();
        $tsfe->initFEuser();
        $tsfe->determineId();
        $tsfe->initTemplate();
        $tsfe->getConfigArray();

        // Set linkVars, absRefPrefix, etc
        if (method_exists('PageGenerator', 'pagegenInit')) {
            PageGenerator::pagegenInit();
        }
    }
}
