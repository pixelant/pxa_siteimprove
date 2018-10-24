<?php

$settings = \Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService::getSettings();
$debugMode = (isset($settings['debugMode'])) ? (bool)$settings['debugMode'] : false;

if ($debugMode === false
    && \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR') != $_SERVER['SERVER_ADDR']
) {
    header('HTTP/1.0 403 Access denied');
    // Empty output!!!
} else {
    /** @var \Pixelant\PxaSiteimprove\Service\PageUrlEidService $pagePathResolverService */
    $pagePathResolverService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Pixelant\PxaSiteimprove\Service\PageUrlEidService::class
    );

    echo $pagePathResolverService->getLink();
}
