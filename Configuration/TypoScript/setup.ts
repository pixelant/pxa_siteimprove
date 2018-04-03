
# Module configuration
module.tx_pxasiteimprove_web_pxasiteimprovedashboard {
    persistence {
        storagePid = {$module.tx_pxasiteimprove_dashboard.persistence.storagePid}
    }
    view {
        templateRootPaths.0 = EXT:pxa_siteimprove/Resources/Private/Backend/Templates/
        templateRootPaths.1 = {$module.tx_pxasiteimprove_dashboard.view.templateRootPath}
        partialRootPaths.0 = EXT:pxa_siteimprove/Resources/Private/Backend/Partials/
        partialRootPaths.1 = {$module.tx_pxasiteimprove_dashboard.view.partialRootPath}
        layoutRootPaths.0 = EXT:pxa_siteimprove/Resources/Private/Backend/Layouts/
        layoutRootPaths.1 = {$module.tx_pxasiteimprove_dashboard.view.layoutRootPath}
    }
}
