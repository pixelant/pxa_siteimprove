<?php

namespace Pixelant\PxaSiteimprove\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Miscellaneous functions relating to compatibility with different TYPO3 versions
 *
 * @extensionScannerIgnoreFile
 */
class CompatibilityUtility
{
    /**
     * Returns the absolute public URL to a page
     *
     * @param $pageId
     * @return string The absolute public URL to page $pageId
     */
    public static function getPageUrl($pageId)
    {
        if (self::typo3VersionIsGreaterThanOrEqualTo('9.5')) {
            /** @var SiteFinder $siteFinder */
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);

            try {
                $site = $siteFinder->getSiteByPageId($pageId);
                $pageLink = (string) $site->getRouter()->generateUri($pageId);
            } catch (SiteNotFoundException $siteNotFoundException) {
                $pageLink = '';
            }

            if ($pageLink !== '' || self::typo3VersionIsGreaterThanOrEqualTo('10.0')) {
                return $pageLink;
            }
        }

        $tsfeWasSet = $GLOBALS['TSFE'] !== null;

        if (!$tsfeWasSet) {
            if (self::typo3VersionIsLessThan('8.0')) {
                if (!is_object($GLOBALS['TT'])) {
                    $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker();
                    $GLOBALS['TT']->start();
                }
            }

            /** @var TypoScriptFrontendController $tsfe */
            $tsfe = GeneralUtility::makeInstance(
                TypoScriptFrontendController::class,
                [],
                $pageId,
                0,
                true
            );

            $GLOBALS['TSFE'] = $tsfe;

            $tsfe->connectToDB();
            $tsfe->initFEuser();
            $tsfe->determineId();
            $tsfe->initTemplate();
            $tsfe->getConfigArray();

            // Set linkVars, absRefPrefix, etc
            if (method_exists('PageGenerator', 'pagegenInit')) {
                PageGenerator::pagegenInit();
            }
        }

        /** @var ContentObjectRenderer $cObj */
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        $linkString = 't3://page?uid=' . $pageId;

        if (self::typo3VersionIsLessThan('8.0')) {
            $linkString = (string)$pageId;
        }

        $typoLinkConf = [
            'parameter' => $linkString,
            'forceAbsoluteUrl' => 1
        ];

        $url = $cObj->typoLink_URL($typoLinkConf) ?: '/';
        $parts = parse_url($url);

        if (!$tsfeWasSet) {
            unset($GLOBALS['TSFE']);
        }

        return empty($parts['host']) ? GeneralUtility::locationHeaderUrl($url) : $url;
    }

    /**
     * Returns the first available domain in the rootline from $pageId
     *
     * @param $pageId
     * @return string
     */
    public static function getFirstDomainInRootline($pageId)
    {
        if (self::typo3VersionIsLessThan('9.4')) {
            $rootLine = BackendUtility::BEgetRootLine($pageId);

            return BackendUtility::firstDomainRecord($rootLine);
        }

        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($pageId);

        return $site->getBase()->getHost();
    }

    /**
     * Return the application context
     *
     * @return \TYPO3\CMS\Core\Core\ApplicationContext
     */
    public static function getApplicationContext()
    {
        if (self::typo3VersionIsLessThan('10.2')) {
            return GeneralUtility::getApplicationContext();
        }

        return Environment::getContext();
    }

    /**
     * Return if siteimprove enabled for BE user
     *
     * @return boolean
     */
    public static function isEnabledForBEUser()
    {
        if (self::typo3VersionIsLessThan('8')) {
            return ((int)$GLOBALS['BE_USER']->uc['use_siteimprove'] === 1
                     && !$GLOBALS['BE_USER']->getTSConfigVal('options.siteImprove.disable'));
        }

        return ((int)$GLOBALS['BE_USER']->uc['use_siteimprove'] === 1
                && (!isset($GLOBALS['BE_USER']->getTSConfig()['options.']['siteImprove.']['disable'])
                || !$GLOBALS['BE_USER']->getTSConfig()['options.']['siteImprove.']['disable']));
    }

    /**
     * Returns true if the current TYPO3 version is less than $version
     *
     * @param string $version
     * @return bool
     */
    public static function typo3VersionIsLessThan($version)
    {
        return self::getTypo3VersionInteger() < VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    /**
     * Returns true if the current TYPO3 version is less than or equal to $version
     *
     * @param string $version
     * @return bool
     */
    public static function typo3VersionIsLessThanOrEqualTo($version)
    {
        return self::getTypo3VersionInteger() <= VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    /**
     * Returns true if the current TYPO3 version is greater than $version
     *
     * @param string $version
     * @return bool
     */
    public static function typo3VersionIsGreaterThan($version)
    {
        return self::getTypo3VersionInteger() > VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    /**
     * Returns true if the current TYPO3 version is greater than or equal to $version
     *
     * @param string $version
     * @return bool
     */
    public static function typo3VersionIsGreaterThanOrEqualTo($version)
    {
        return self::getTypo3VersionInteger() >= VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    /**
     * Returns the TYPO3 version as an integer
     *
     * @return int
     */
    public static function getTypo3VersionInteger()
    {
        return VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getNumericTypo3Version());
    }
}
