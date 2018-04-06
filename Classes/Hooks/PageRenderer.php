<?php
declare(strict_types=1);

namespace Pixelant\PxaSiteimprove\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class which adds the necessary resources for Siteimprove (https://siteimprove.com/).
 */
class PageRenderer implements SingletonInterface
{
    /**
     * Wrapper function called by hook (\TYPO3\CMS\Core\Page\PageRenderer->render-preProcess)
     *
     * @param array $parameters An array of available parameters
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer The parent object that triggered this hook
     */
    public function addResources(array $parameters, \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer)
    {
        // Add the resources only to the 'Page' module
        if (get_class($GLOBALS['SOBE']) === PageLayoutController::class || is_subclass_of($GLOBALS['SOBE'],
                PageLayoutController::class)) {
            $pageId = (int)GeneralUtility::_GP('id');
            if ($pageId !== null) {
                $siteRoot = 0;
                $pageRootLineStructure = BackendUtility::BEgetRootLine($pageId);
                foreach ($pageRootLineStructure as $pageRootLine) {
                    if ((bool)$pageRootLine['is_siteroot'] === true) {
                        $siteRoot = $pageRootLine;
                        break;
                    }
                }
                $pageInformation = BackendUtility::getRecord('pages', $pageId);
            }

            $pageRenderer->addJsFile('https://cdn.siteimprove.net/cms/overlay.js');
            $siteimproveOnDomReady = "
                $(document).ready(function() {
                    var _si = window._si || [];
                    $.ajax({
                            url: 'https://my2.siteimprove.com/auth/token?cms=TYPO3 8'
                        })
                        .done(function(data) {
                        if (data.token) {
                            _si.push(['domain', 'www.pixelant.se', data.token, function() { console.log('https://pixelant.se'); }])
                            }
                    });
                    // if (window._si !== undefined) { window._si.push(['showlog','']); }
                });";
            $pageRenderer->addJsInlineCode('siteimproveOnDomReady', $siteimproveOnDomReady);
        }
    }

    /**
     * Gets the current backend user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    public function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * getter for language service
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    public function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
