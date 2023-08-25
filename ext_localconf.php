<?php

defined('TYPO3') or die();

// Register autoloading for TypoScript for TYPO3 v11
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Core/TypoScript/TemplateService']['runThroughTemplatesPostProcessing']['unbolt'] = \Libeo\Unbolt\TypoScript\Loader::class . '->addSiteConfiguration';
