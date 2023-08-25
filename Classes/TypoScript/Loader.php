<?php

declare(strict_types=1);

namespace Libeo\Unbolt\TypoScript;

use B13\Bolt\Configuration\PackageHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TypoScript\TemplateService;

class Loader
{
    protected PackageHelper $packageHelper;
    protected ConnectionPool $connectionPool;

    public function __construct(PackageHelper $packageHelper, ConnectionPool $connectionPool)
    {
        $this->packageHelper = $packageHelper;
        $this->connectionPool = $connectionPool;
    }

    /**
     *  $hookParameters = [
     *      'extensionStaticsProcessed' => &$this->extensionStaticsProcessed,
     *      'isDefaultTypoScriptAdded'  => &$this->isDefaultTypoScriptAdded,
     *      'absoluteRootLine' => &$this->absoluteRootLine,
     *      'rootLine'         => &$this->rootLine,
     *      'startTemplateUid' => $start_template_uid,
     *  ];
     * @param array $hookParameters
     * @param TemplateService $templateService
     */
    public function addSiteConfiguration(&$hookParameters, TemplateService $templateService): void
    {
        // let's copy the rootline value, as $templateService->processTemplate() might reset it
        $rootLine = $hookParameters['rootLine'] ?? null;
        if (!is_array($rootLine) || empty($rootLine)) {
            return;
        }

        foreach ($rootLine as $level => $pageRecord) {
            $package = $this->packageHelper->getSitePackage((int)$pageRecord['uid']);
            if ($package !== null) {
                // Load sys_template from DB
                $connection = $this->connectionPool->getConnectionForTable('sys_template');
                $templateDb = $connection->select(
                    columns: ['uid', 'constants', 'tstamp', 'title'],
                    tableName: 'sys_template',
                    identifiers: [
                        'pid' => (int)$pageRecord['uid'],
                        'hidden' => 0,
                        'deleted' => 0
                    ],
                    orderBy: ['sorting' => 'ASC'],
                    limit: 1
                )->fetchAssociative();

                if($templateDb){
                    $fakeRow = [
                        'config' => '', // always empty
                        'constants' => $templateDb['constants'], // from db
                        'nextLevel' => 0,
                        'static_file_mode' => 1,
                        'tstamp' => $templateDb['tstamp'], // from db
                        'uid' => $templateDb['uid'], // from db
                        'title' => $templateDb['title'], // from db
                    ];

                    $templateService->processTemplate(
                        $fakeRow,
                        'sys_unbolt_' . $package->getPackageKey(),
                        (int)$pageRecord['uid'],
                        'sys_unbolt_' . $package->getPackageKey()
                    );
                }
            }
        }
    }
}
