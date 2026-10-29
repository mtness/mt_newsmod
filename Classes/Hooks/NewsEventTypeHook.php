<?php
declare(strict_types=1);

namespace MarkusTimtner\MtNewsmod\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Keeps eventnews' is_event flag in line with the news type "event" (3),
 * because the eventnews frontend queries only know the flag.
 */
final class NewsEventTypeHook
{
    public const TABLE = 'tx_news_domain_model_news';
    public const TYPE_EVENT = 3;

    public function processDatamap_postProcessFieldArray(
        string $status,
        string $table,
        string|int $id,
        array &$fieldArray,
        DataHandler $dataHandler
    ): void {
        if ($table !== self::TABLE || !array_key_exists('type', $fieldArray)) {
            return;
        }

        $fieldArray['is_event'] = (int)$fieldArray['type'] === self::TYPE_EVENT ? 1 : 0;
    }
}
