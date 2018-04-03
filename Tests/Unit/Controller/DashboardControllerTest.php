<?php
namespace Pixelant\PxaSiteimprove\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Mattias Nilsson <mattias@pixelant.se>
 */
class DashboardControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSiteimprove\Controller\DashboardController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Pixelant\PxaSiteimprove\Controller\DashboardController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllDashboardsFromRepositoryAndAssignsThemToView()
    {

        $allDashboards = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dashboardRepository = $this->getMockBuilder(\Pixelant\PxaSiteimprove\Domain\Repository\DashboardRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $dashboardRepository->expects(self::once())->method('findAll')->will(self::returnValue($allDashboards));
        $this->inject($this->subject, 'dashboardRepository', $dashboardRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('dashboards', $allDashboards);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenDashboardToView()
    {
        $dashboard = new \Pixelant\PxaSiteimprove\Domain\Model\Dashboard();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('dashboard', $dashboard);

        $this->subject->showAction($dashboard);
    }
}
