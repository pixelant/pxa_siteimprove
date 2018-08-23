<?php

if (\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR') != $_SERVER['SERVER_ADDR']) {
    header('HTTP/1.0 403 Access denied');
    // Empty output!!!
} else {
    /** @var \Pixelant\PxaSiteimprove\Service\PageUrlEidService $pagePathResolverService */
    $pagePathResolverService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Pixelant\PxaSiteimprove\Service\PageUrlEidService::class
    );

    echo $pagePathResolverService->getLink();
}
