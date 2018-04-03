<?php
namespace Siteimprove\PxaSiteimprove\Controller;

/***
 *
 * This file is part of the "Siteimprove" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Mattias Nilsson <mattias@pixelant.se>, Pixelant AB
 *
 ***/

/**
 * DashboardController
 */
class DashboardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * dashboardRepository
     *
     * @var \Siteimprove\PxaSiteimprove\Domain\Repository\DashboardRepository
     * @inject
     */
    protected $dashboardRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $dashboards = $this->dashboardRepository->findAll();
        $this->view->assign('dashboards', $dashboards);
    }

    /**
     * action show
     *
     * @param \Siteimprove\PxaSiteimprove\Domain\Model\Dashboard $dashboard
     * @return void
     */
    public function showAction(\Siteimprove\PxaSiteimprove\Domain\Model\Dashboard $dashboard)
    {
        $this->view->assign('dashboard', $dashboard);
    }
}
