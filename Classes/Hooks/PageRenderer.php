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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService;
use DmitryDulepov\Realurl\Cache\DatabaseCache;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
        if (isset($GLOBALS['SOBE']) && get_class($GLOBALS['SOBE']) === PageLayoutController::class
            || is_subclass_of($GLOBALS['SOBE'], PageLayoutController::class)) {
            $settings = ExtensionManagerConfigurationService::getSettings();
            $debugMode = (isset($settings['debugMode'])) ? (bool)$settings['debugMode'] : false;
            $domain = '';
            $url = '';
            $pageId = (int)$GLOBALS['SOBE']->id;
            if ($pageId !== null) {
                $rootLine = BackendUtility::BEgetRootLine($pageId);
                $rootLineEntry = $rootLine[1];
                $domain = BackendUtility::firstDomainRecord($rootLine);

                $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
                $typoLinkConf = [
                    'parameter' => $pageId,
                    'forceAbsoluteUrl' => 1
                ];
                $url = $contentObjectRenderer->typoLink_URL($typoLinkConf) ?: '/';

                // If the page is the same as the root, do not add ?id=1 to path
                if ($rootLineEntry['uid'] === $pageId) {
                    $url = parse_url($url);
                    $url = sprintf(
                        '%s://%s%s/',
                        $url['scheme'],
                        $url['host'],
                        ($url['port'] == '') ? '' : ':' . $url['port']
                    );
                }

                // If realurl is loaded then resolve the page path (nice urls)
                if (ExtensionManagementUtility::isLoaded('realurl')) {
                    /** @var DatabaseCache $databaseCache */
                    $databaseCache = GeneralUtility::makeInstance(DatabaseCache::class);
                    $pagePath = $databaseCache->getPathFromCacheByPageId(
                        $rootLineEntry,
                        $GLOBALS['SOBE']->current_sys_language,
                        $pageId,
                        []
                    );

                    // If a cached page path was found
                    if ($pagePath !== null) {
                        $parsedUrl = parse_url($url);
                        $url = sprintf(
                            '%s://%s%s/%s',
                            $parsedUrl['scheme'],
                            $parsedUrl['host'],
                            ($parsedUrl['port'] == '') ? '' : ':' . $parsedUrl['port'],
                            $pagePath->getPagePath()
                        );
                    }
                }
            }

            $debugScript = '';
            if ($debugMode === true) {
                $debugScript = "if (window._si !== undefined) { window._si.push(['showlog','']); }";
            }

            $siteimproveOnDomReady = "
                $(document).ready(function() {
                    var _si = window._si || [];
                    $.ajax({
                        url: 'https://my2.siteimprove.com/auth/token?cms=TYPO3 8',
                    })
                    .done(function(data) {
                        if (data.token) {
                            _si.push(['domain', '" . $domain .
                                "', data.token, function() { console.log('Domain logged: " . $domain . "'); }]);
                            _si.push(['input', '" . $url .
                                "', data.token, function() { console.log('Inputted url: " . $url . "'); }])
                        }
                    });
                    " . $debugScript . "
                });";

            // Add overlay.js none concatenated
            $pageRenderer->addJsFooterLibrary(
                'SiteimproveOverlay',
                'https://cdn.siteimprove.net/cms/overlay.js',
                'text/javascript',
                false,
                true,
                '',
                true
            );
            $pageRenderer->addJsInlineCode('siteimproveOnDomReady', $siteimproveOnDomReady);
        }
    }

    /**
     * Gets the current backend user
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    public function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Getter for language service
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    public function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
