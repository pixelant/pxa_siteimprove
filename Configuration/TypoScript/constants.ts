
module.tx_pxasiteimprove_dashboard {
    view {
        # cat=module.tx_pxasiteimprove_dashboard/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:pxa_siteimprove/Resources/Private/Backend/Templates/
        # cat=module.tx_pxasiteimprove_dashboard/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:pxa_siteimprove/Resources/Private/Backend/Partials/
        # cat=module.tx_pxasiteimprove_dashboard/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:pxa_siteimprove/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.tx_pxasiteimprove_dashboard//a; type=string; label=Default storage PID
        storagePid =
    }
}
