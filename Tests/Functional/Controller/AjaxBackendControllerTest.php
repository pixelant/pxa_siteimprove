<?php


namespace Pixelant\PxaSiteimprove\Tests\Functional\Controller;


use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaSiteimprove\Controller\AjaxBackendController;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Core\Bootstrap;

class AjaxBackendControllerTest extends FunctionalTestCase
{
    /**
     * @var AjaxBackendController
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->importDataSet('PACKAGE:nimut/testing-framework/res/Fixtures/Database/pages.xml');
        $this->importDataSet('PACKAGE:nimut/testing-framework/res/Fixtures/Database/sys_language.xml');

        $this->setUpBackendUserFromFixture(1);
        $this->setUpFrontendRootPage(
            1,
            ['PACKAGE:nimut/testing-framework/res/Fixtures/TypoScript/JsonRenderer.ts'],
            ['PACKAGE:nimut/testing-framework/res/Fixtures/Sites/Frontend/site.yaml']
        );
        Bootstrap::initializeLanguageObject();

        $this->subject = new AjaxBackendController();
    }

    /**
     * @test
     */
    public function getPageLinkActionReturnsCorrectUrl()
    {
        $request = new ServerRequest();
        $request->withQueryParams(['id' => 1]);

        $response = $this->subject->getPageLinkAction($request);
        $body = (string)$response->getBody();
        $jsonArray = json_decode($body, true);

        var_dump($body);
    }
}
