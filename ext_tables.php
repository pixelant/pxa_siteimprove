<?php
defined('TYPO3_MODE') || die('Access denied.');

if (!isset($_EXTKEY)) {
    $_EXTKEY = 'pxa_siteimprove';
}

// Add BE User setting
$GLOBALS['TYPO3_USER_SETTINGS']['columns']['use_siteimprove'] = [
    'label' => 'LLL:EXT:pxa_siteimprove/Resources/Private/Language/locallang.xlf:settings.field.use_siteimprove',
   'type' => 'check',
   'default' => 0
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
    'use_siteimprove',
    'after:edit_RTE'
);
call_user_func(
    function () {
        if (TYPO3_MODE === 'BE') {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] =
                'Pixelant\\PxaSiteimprove\\Hooks\\PageRenderer->addResources';
        }
    }
);
