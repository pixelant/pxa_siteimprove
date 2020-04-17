<?php


namespace Pixelant\PxaSiteimprove\Tests\Functional\Controller;


use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaSiteimprove\Controller\AjaxBackendController;
use Pixelant\PxaSiteimprove\Utility\CompatibilityUtility;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AjaxBackendControllerTest extends FunctionalTestCase
{
    /**
     * @var AjaxBackendController
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        if (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(9500000)) {
            $this->importDataSet('/home/runner/work/pxa_siteimprove/pxa_siteimprove/Tests/Fixtures/Database/pages.xml');
        } else {
            $this->importDataSet('/home/runner/work/pxa_siteimprove/pxa_siteimprove/Tests/Fixtures/Database/pages-legacy.xml');
        }

        if (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(10000000)) {
            $this->setUpFrontendRootPage(1);
        } else {
            $this->setUpFrontendRootPage(
                1,
                ['/home/runner/work/pxa_siteimprove/pxa_siteimprove/.Build/vendor/nimut/testing-framework/res/Fixtures/TypoScript/JsonRenderer.ts']
            );
        }

        $this->setUpBackendUserFromFixture(1);

        Bootstrap::initializeLanguageObject();

        $this->subject = new AjaxBackendController();
    }

    /**
     * @test
     */
    public function getPageLinkActionReturnsCorrectUrl()
    {
        $request = (new ServerRequest())->withQueryParams(['id' => 2]);
        $response = $this->subject->getPageLinkAction($request);
        $body = (string)$response->getBody();

        if (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(10000000)) {
            $this->assertEquals(
                '{"pageUrl":"/dummy-1-2"}',
                $body
            );
        } else {
            $this->assertEquals(
                '{"pageUrl":"/dummy-1-2"}',
                $body
            );
        }
    }
}
