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

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService;

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
     * @throws \Exception
     */
    public function addResources(array $parameters, \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer)
    {
        // Add the resources only to the 'Page' module
        if (isset($GLOBALS['SOBE']) && get_class($GLOBALS['SOBE']) === PageLayoutController::class
            || is_subclass_of($GLOBALS['SOBE'], PageLayoutController::class)) {
            // Check if the user has enabled Siteimprove in the user settings, and it is not disabled for the user group
            if ((int)$GLOBALS['BE_USER']->uc['use_siteimprove'] === 1
                && !$GLOBALS['BE_USER']->getTSConfigVal('options.siteImprove.disable')
            ) {
                $settings = ExtensionManagerConfigurationService::getSettings();
                $debugMode = (isset($settings['debugMode'])) ? (bool)$settings['debugMode'] : false;
                $domain = '';
                $url = '';
                $pageId = (int)$GLOBALS['SOBE']->id;
                if ($pageId > 0) {
                    $rootLine = BackendUtility::BEgetRootLine($pageId);
                    $domain = BackendUtility::firstDomainRecord($rootLine);

                    $cache = $this->getCache();

                    $eidUrl = $this->getEidUrl($pageId, $domain);
                    $cacheIdentifier = sha1($eidUrl);

                    if ($cache->has($cacheIdentifier)) {
                        $url = $cache->get($cacheIdentifier);
                    } else {
                        $url = trim(GeneralUtility::getUrl($eidUrl));
                        $cache->set($cacheIdentifier, $url);
                    }
                }

                $debugScript = '';
                if ($debugMode === true) {
                    $debugScript = "if (window._si !== undefined) { window._si.push(['showlog','']); }";
                }

                $siteimproveOnDomReady = "
                var jquery = TYPO3.jQuery;
                jquery(document).ready(function() {
                    var _si = window._si || [];
                    jquery.ajax({
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
                $pageRenderer->loadJquery();

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
        $data = [
            'id' => $pageUid
        ];

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

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
            '%s://%s/index.php?eID=pxa_siteimprove&data=%s',
            $scheme,
            $domain,
            base64_encode(json_encode($data))
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
