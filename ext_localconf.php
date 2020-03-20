<?php
defined('TYPO3_MODE') || die('Access denied.');

// Register EID
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Eid/LinkGeneratorEid.php';

// @codingStandardsIgnoreStart
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pxasiteimprove_urls']['frontend'])) {// @codingStandardsIgnoreEnd
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pxasiteimprove_urls'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'options' => [
            'defaultLifetime' => 3600 * 24 // One day
        ],
        'groups' => ['all']
    ];
}

if (\Pixelant\PxaSiteimprove\Utility\CompatibilityUtility::typo3VersionIsGreaterThanOrEqualTo(8000000)) {
    if(\Pixelant\PxaSiteimprove\Utility\CompatibilityUtility::typo3VersionIsLessThan(9500000)) {
        // Handled by middleware in >=9.5
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
            \Pixelant\PxaSiteimprove\Hooks\DeepLinkingHandler::class . '->storeGoToRequestInUserSession';
    }
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['constructPostProcess'][]
        = \Pixelant\PxaSiteimprove\Hooks\DeepLinkingHandler::class . '->effectuateGoToRequest';
}
