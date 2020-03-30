<?php


namespace Pixelant\PxaSiteimprove\Tests\Functional\Controller;


use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaSiteimprove\Controller\AjaxBackendController;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\SiteFinder;
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

        $this->importDataSet('/home/runner/work/pxa_siteimprove/pxa_siteimprove/.Build/vendor/nimut/testing-framework/res/Fixtures/Database/pages.xml');

        $this->setUpBackendUserFromFixture(1);
        $this->setUpFrontendRootPage(1);
        Bootstrap::initializeLanguageObject();

        $this->subject = new AjaxBackendController();
    }

    /**
     * @test
     */
    public function getPageLinkActionReturnsCorrectUrl()
    {
        $request = (new ServerRequest())
            ->withUri(new Uri('https://www.example.com/'))
            ->withQueryParams(['id' => 2]);
        $response = $this->subject->getPageLinkAction($request);
        $body = (string)$response->getBody();
        $jsonArray = json_decode($body, true);

        var_dump($body);
    }
}
