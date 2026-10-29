<?php

declare(strict_types=1);

use GeorgRinger\News\Domain\Model\News;
use MarkusTimtner\MtNewsmod\Domain\Model\NewsEvent;
use MarkusTimtner\MtNewsmod\Hooks\NewsEventTypeHook;

// Extbase restricts queries on News to the record types of its subclasses (0, 1, 2),
// so records of type 3 would silently vanish from every list without this.
return [
    News::class => [
        'subclasses' => [
            NewsEventTypeHook::TYPE_EVENT => NewsEvent::class,
        ],
    ],
    NewsEvent::class => [
        'tableName' => 'tx_news_domain_model_news',
        'recordType' => NewsEventTypeHook::TYPE_EVENT,
    ],
];
