<?php

namespace Pixelant\PxaSiteimprove\Tests\Functional\Controller;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaSiteimprove\Controller\AjaxBackendController;
use Pixelant\PxaSiteimprove\Utility\CompatibilityUtility;
use TYPO3\CMS\Core\Http\ServerRequest;

class AjaxBackendControllerTest extends FunctionalTestCase
{
    /**
     * @var AjaxBackendController
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $rootPath = '/home/runner/work/pxa_siteimprove/pxa_siteimprove/';

        if (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(9500000)) {
            $this->importDataSet($rootPath . 'Tests/Fixtures/Database/pages.xml');
        } else {
            $this->importDataSet($rootPath . 'Tests/Fixtures/Database/pages-legacy.xml');
        }

        if (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(10000000)) {
            $this->setUpFrontendRootPage(1);
        } elseif (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(9500000)) {
            $this->setUpFrontendRootPage(
                1,
                [$rootPath . '.Build/vendor/nimut/testing-framework/res/Fixtures/TypoScript/JsonRenderer.ts'],
                [$rootPath . '.Build/vendor/nimut/testing-framework/res/Fixtures/Frontend/Site.yaml']
            );
        } else {
            $this->setUpFrontendRootPage(
                1,
                [$rootPath . '.Build/vendor/nimut/testing-framework/res/Fixtures/TypoScript/JsonRenderer.ts']
            );
        }

        $this->setUpBackendUserFromFixture(1);

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

        if (is_object($body)) {
            var_dump($body);
            die();
        }


        if (CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(10000000)) {
            $this->assertEquals(
                '{"pageUrl":"\/dummy-1-2"}',
                $body
            );
        } else {
            $this->assertEquals(
                '{"pageUrl":"http:\/\/Build\/bin\/index.php?id=2"}',
                $body
            );
        }
    }
}
