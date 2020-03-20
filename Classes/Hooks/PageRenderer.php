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
            isset($GLOBALS['SOBE']) && get_class($GLOBALS['SOBE']) === PageLayoutController::class
            || is_subclass_of($GLOBALS['SOBE'], PageLayoutController::class)
        ) {
            // Check if the user has enabled Siteimprove in the user settings, and it is not disabled for the user group
            if (
                (int)$GLOBALS['BE_USER']->uc['use_siteimprove'] === 1
                && !$GLOBALS['BE_USER']->getTSConfig()['options.']['siteImprove.']['disable']
            ) {
                $settings = ExtensionManagerConfigurationService::getSettings();
                $debugMode = (isset($settings['debugMode'])) ? (bool)$settings['debugMode'] : false;
                $domain = '';
                $url = '';
                $pageId = (int)$GLOBALS['SOBE']->id;

                if ($pageId > 0) {
                    $domain = CompatibilityUtility::getFirstDomainInRootline($pageId);

                    $eidUrl = $this->getEidUrl($pageId, $domain);

                    $debugScript = '';
                    if ($debugMode === true) {
                        $debugScript = "if (window._si !== undefined) { window._si.push(['showlog','']); }";
                    }

                    $token = (isset($settings['token']) && $settings['token']) ? $settings['token'] : self::DEFAULT_TOKEN;

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
                                    _si.push(['input', data.pageUrl, token, function() { console.log('Inputted url: ' + data.pageUrl); }])
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
     * Get eid url to fetch FE page ID url
     *
     * @param $pageUid
     * @param $domain
     * @return string
     */
    protected function getEidUrl($pageUid, $domain)
    {
        //Define scheme
        $reverseProxyIP = explode(',', $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP']);
        $reverseProxySSL = explode(',', $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxySSL']);
        $ipOfProxyOrClient = $_SERVER['REMOTE_ADDR'];

        if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (in_array($ipOfProxyOrClient, $reverseProxyIP) && isset($ipOfProxyOrClient) &&
                (in_array($ipOfProxyOrClient, $reverseProxySSL) || $reverseProxySSL[0] === '*'))
        ) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        if (!empty($domain)) {
            $port = GeneralUtility::getIndpEnv('TYPO3_PORT');
            if (!empty($port)) {
                // Check if the domain already contains a port if so do not add port
                if (!preg_match('/\:\b/', $domain)) {
                    $domain .= ':' . $port;
                }
            }
        } else {
            $domain = GeneralUtility::getIndpEnv('HTTP_HOST');
        }

        $eidUrl = sprintf(
            '%s://%s/index.php?eID=pxa_siteimprove&id=%s',
            $scheme,
            $domain,
            (int) $pageUid
        );

        return $eidUrl;
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
