<?php

namespace Pixelant\PxaSiteimprove\Tests\Unit\Hooks;

use Pixelant\PxaSiteimprove\Hooks\PageRenderer;
use TYPO3\CMS\Core\Page\PageRenderer as BackendPageRenderer;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

class PageRendererTest extends UnitTestCase
{

    /**
     * Test to get a backend user
     *
     * @test
     */
    public function getBackendUser()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $GLOBALS['BE_USER'] = GeneralUtility::makeInstance(BackendUserAuthentication::class);
        $result = $pageRenderer->getBackendUser();
        $this->assertEquals($GLOBALS['BE_USER'], $result);
    }

    /**
     * Test get the language service
     *
     * @test
     */
    public function getLanguageService()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $result = $pageRenderer->getLanguageService();
        $this->assertEquals($GLOBALS['LANG'], $result);
    }

    /**
     * Test for adding the Siteimprove resources
     *
     * @test
     */
    public function addResources()
    {
        $backendPageRenderer = new BackendPageRenderer();
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addResources([], $backendPageRenderer);
        $content = $backendPageRenderer->render();
        $this->assertGreaterThan(0, strlen($content));
    }

    /**
     * @test
     */
    public function generateEdiUrlWithDomain()
    {
        $pageRenderer = $this->getAccessibleMock(
            PageRenderer::class,
            ['dummy']
        );

        $domain = 'www.site.com';
        $pageUid = 12;
        $data = [
            'id' => $pageUid
        ];

        $expect = 'http://www.site.com/index.php?eID=pxa_siteimprove&data=' . base64_encode(json_encode($data));

        $this->assertEquals($expect, $pageRenderer->_call('getEidUrl', $pageUid, $domain));
    }
}
