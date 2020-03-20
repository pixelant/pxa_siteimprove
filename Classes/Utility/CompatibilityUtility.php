<?php


namespace Pixelant\PxaSiteimprove\Utility;


use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Miscellaneous functions relating to compatibility with different TYPO3 versions
 *
 * @extensionScannerIgnoreFile
 */
class CompatibilityUtility
{
    /**
     * Returns the first available domain in the rootline from $pageId
     *
     * @param $pageId
     * @return string
     */
    public static function getFirstDomainInRootline($pageId)
    {
        if (self::typo3VersionIsLessThan(9400000)) {
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
        if (self::typo3VersionIsLessThan(10002000)) {
            return GeneralUtility::getApplicationContext();
        }

        return Environment::getContext();
    }

    /**
     * Returns true if the current TYPO3 version is less than $version
     *
     * @param int $version
     * @return bool
     */
    public static function typo3VersionIsLessThan($version)
    {
        return self::getTypo3VersionInteger() < $version;
    }

    /**
     * Returns true if the current TYPO3 version is less than or equal to $version
     *
     * @param int $version
     * @return bool
     */
    public static function typo3VersionIsLessThanOrEqualTo($version)
    {
        return self::getTypo3VersionInteger() <= $version;
    }

    /**
     * Returns true if the current TYPO3 version is greater than $version
     *
     * @param int $version
     * @return bool
     */
    public static function typo3VersionIsGreaterThan(i$version)
    {
        return self::getTypo3VersionInteger() > $version;
    }

    /**
     * Returns true if the current TYPO3 version is greater than or equal to $version
     *
     * @param int $version
     * @return bool
     */
    public static function typo3VersionIsGreaterThanOrEqualTo($version)
    {
        return self::getTypo3VersionInteger() >= $version;
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
