<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
# Enable only on root pages
[traverse(page, "uid") == site("rootPageId")]
    # Enable adding new sys_template records in list module
    mod.web_list.deniedNewTables := removeFromList(sys_template)

    # Unhide tstemplate "Info/Modify" and "Constant Editor" in core v11
    mod.web_ts.menu.function.TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateConstantEditorModuleFunctionController = 1
    mod.web_ts.menu.function.TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateInformationModuleFunctionController = 1
[global]

# Hide all fields of sys_template to keep only title, constants and notes
TCEFORM.sys_template {
    config.disabled=1
    clear.disabled=1
    root.disabled=1
    includeStaticAfterBasedOn.disabled=1
    include_static_file.disabled=1
    basedOn.disabled=1
    static_file_mode.disabled=1
    hidden.disabled=1
    starttime.disabled=1
    endtime.disabled=1
}
');
