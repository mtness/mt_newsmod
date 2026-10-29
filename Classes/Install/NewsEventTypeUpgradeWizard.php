<?php
declare(strict_types=1);

namespace MarkusTimtner\MtNewsmod\Install;

use MarkusTimtner\MtNewsmod\Hooks\NewsEventTypeHook;
use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('mtNewsmodEventType')]
final class NewsEventTypeUpgradeWizard implements UpgradeWizardInterface
{
    public function __construct(private readonly ConnectionPool $connectionPool) {}

    public function getTitle(): string
    {
        return 'MT Newsmod: set news type "event" for existing events';
    }

    public function getDescription(): string
    {
        return 'Sets type=3 on all news records with is_event=1, so they use the event backend layout.';
    }

    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    public function updateNecessary(): bool
    {
        $queryBuilder = $this->affectedRecords();

        return (int)$queryBuilder->count('uid')->executeQuery()->fetchOne() > 0;
    }

    public function executeUpdate(): bool
    {
        $affected = $this->affectedRecords()->select('uid')->executeQuery()->fetchFirstColumn();
        $connection = $this->connectionPool->getConnectionForTable(NewsEventTypeHook::TABLE);

        foreach ($affected as $uid) {
            $connection->update(
                NewsEventTypeHook::TABLE,
                ['type' => (string)NewsEventTypeHook::TYPE_EVENT],
                ['uid' => (int)$uid],
                ['type' => Connection::PARAM_STR, 'uid' => Connection::PARAM_INT]
            );
        }

        return true;
    }

    /** Deleted and hidden records are migrated as well. */
    private function affectedRecords(): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(NewsEventTypeHook::TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder
            ->from(NewsEventTypeHook::TABLE)
            ->where(
                $queryBuilder->expr()->eq('is_event', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq(
                    'type',
                    $queryBuilder->createNamedParameter((string)NewsEventTypeHook::TYPE_EVENT)
                )
            );
    }
}
