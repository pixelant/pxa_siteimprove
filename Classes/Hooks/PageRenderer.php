<?php

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

use Pixelant\PxaSiteimprove\Utility\CompatibilityUtility;
use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService;

/**
 * Class which adds the necessary resources for Siteimprove (https://siteimprove.com/).
 */
class PageRenderer implements SingletonInterface
{
    // @codingStandardsIgnoreLine
    const DEFAULT_TOKEN = '';

    /**
     * Wrapper function called by hook (\TYPO3\CMS\Core\Page\PageRenderer->render-preProcess)
     *
     * @param array $parameters An array of available parameters
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer The parent object that triggered this hook
     * @throws \Exception
     */
    public function addResources(array $parameters, \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer)
    {
        // Add the resources only to the 'Page' module
        if (
            isset($GLOBALS['SOBE'])
            && ($GLOBALS['SOBE'] instanceof PageLayoutController || $GLOBALS['SOBE'] instanceof PageLayoutController)
        ) {
            // Check if the user has enabled Siteimprove in the user settings, and it is not disabled for the user group
            if (
                (int)$GLOBALS['BE_USER']->uc['use_siteimprove'] === 1
                && (!isset($GLOBALS['BE_USER']->getTSConfig()['options.']['siteImprove.']['disable'])
                || !$GLOBALS['BE_USER']->getTSConfig()['options.']['siteImprove.']['disable'])
            ) {
                $settings = ExtensionManagerConfigurationService::getSettings();
                $debugMode = (isset($settings['debugMode'])) ? (bool)$settings['debugMode'] : false;
                $domain = '';
                $url = '';
                $pageId = (int)$GLOBALS['SOBE']->id;

                if ($pageId > 0) {
                    $domain = CompatibilityUtility::getFirstDomainInRootline($pageId);

                    $debugScript = '';
                    if ($debugMode === true) {
                        $debugScript = "if (window._si !== undefined) { window._si.push(['showlog','']); }";
                    }

                    $token
                        = (isset($settings['token']) && $settings['token']) ? $settings['token'] : self::DEFAULT_TOKEN;

                    $siteimproveOnDomReady = "
                    require(['jquery'], function($) {
                        jQuery(document).ready(function() {
                            var _si = window._si || [];
                            var token = '" . $token . "';
                            jQuery.ajax({
                                url: TYPO3.settings.ajaxUrls['pixelant_siteimprove_getpagelink'],
                                data: {
                                    id: " . (int)$pageId . "
                                }
                            })
                            .done(function(data) {
                                if (token) {
                                    _si.push(['domain', '" . $domain .
                            "', token, function() { console.log('Domain logged: " . $domain . "'); }]);
                                    _si.push(['input', data.pageUrl, token, function() {
                                        console.log('Inputted url: ' + data.pageUrl);
                                    }])
                                }
                            });
                            " . $debugScript . '
                        });
                    });';

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
                    $pageRenderer->addJsFooterInlineCode('siteimproveOnDomReady', $siteimproveOnDomReady);
                }
            }
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

    /**
     * @return  FrontendInterface
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    protected function getCache()
    {
        return GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_pxasiteimprove_urls');
    }
}
