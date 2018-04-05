<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        if (TYPO3_MODE === 'BE') {

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Pixelant.PxaSiteimprove',
                'web', // Make module a submodule of 'web'
                'dashboard', // Submodule key
                '', // Position
                [
                    'Dashboard' => 'list, show',
                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:pxa_siteimprove/Resources/Public/Icons/user_mod_dashboard.svg',
                    'labels' => 'LLL:EXT:pxa_siteimprove/Resources/Private/Language/locallang_dashboard.xlf',
                    'navigationComponentId' => 'typo3-pagetree'
                ]
            );

        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('pxa_siteimprove', 'Configuration/TypoScript', 'Siteimprove');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pxasiteimprove_domain_model_dashboard', 'EXT:pxa_siteimprove/Resources/Private/Language/locallang_csh_tx_pxasiteimprove_domain_model_dashboard.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pxasiteimprove_domain_model_dashboard');

    }
);
