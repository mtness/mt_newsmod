<?php
defined('TYPO3') or die();

use MarkusTimtner\MtNewsmod\Hooks\NewsEventTypeHook;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Backend layouts of tx_news_domain_model_news:
 *
 *  type 0  news   one main tab, few extra tabs
 *  type 3  event  same, with all event fields close together below the teaser
 *
 * type 3 is kept in sync with eventnews' is_event flag by
 * MarkusTimtner\MtNewsmod\Hooks\NewsEventTypeHook, existing events are migrated by
 * MarkusTimtner\MtNewsmod\Install\NewsEventTypeUpgradeWizard (mtNewsmodEventType).
 */

$table     = 'tx_news_domain_model_news';
$typeEvent = NewsEventTypeHook::TYPE_EVENT;
$lll       = 'LLL:EXT:mt_newsmod/Resources/Private/Language/locallang_db.xlf:';

ExtensionManagementUtility::addTCAcolumns($table, [
	'subtitle' => [
		'exclude'   => true,
		'label'     => $lll . 'tx_news_domain_model_news.subtitle',
		'l10n_mode' => 'prefixLangTitle',
		'config'    => [
			'type' => 'input',
			'size' => 60,
			'max'  => 255,
			'eval' => 'trim',
		],
	],
]);

$GLOBALS['TCA'][$table]['columns']['type']['config']['items'][] = [
	'label' => $lll . 'tx_news_domain_model_news.type.event',
	'value' => $typeEvent,
	'icon'  => 'ext-news-type-event',
];
$GLOBALS['TCA'][$table]['ctrl']['typeicon_classes'][(string)$typeEvent] = 'ext-news-type-event';

// news adds a "preview" button (customControls) to the content elements field
unset($GLOBALS['TCA'][$table]['columns']['content_elements']['config']['customControls']);

// eventnews appends its own tab and the is_event switch to every type.
// The switch is replaced by type 3, so the URL types lose them again.
foreach (['1', '2'] as $urlType) {
	$showitem = (string)($GLOBALS['TCA'][$table]['types'][$urlType]['showitem'] ?? '');
	$showitem = preg_replace(
		[
			'/,?\s*--div--;[^,]*tab\.eventnews\s*,\s*--palette--;;palette_eventfields\s*/',
			'/\bis_event\s*,\s*--palette--;;palette_event\s*,?/',
		],
		['', ''],
		$showitem
	);
	$GLOBALS['TCA'][$table]['types'][$urlType]['showitem'] = $showitem;
}

$GLOBALS['TCA'][$table]['palettes']['paletteEventDates'] = [
	'label'    => $lll . 'tx_news_domain_model_news.palettes.eventDates',
	'showitem' => 'datetime, event_end, full_day',
];
$GLOBALS['TCA'][$table]['palettes']['paletteEventPlace'] = [
	'label'    => $lll . 'tx_news_domain_model_news.palettes.eventPlace',
	'showitem' => 'location, location_simple, --linebreak--, organizer, organizer_simple',
];

// publish / expiration dates sit with the dates instead of the access tab
$GLOBALS['TCA'][$table]['palettes']['paletteNewsDates'] = [
	'label'    => 'LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.palettes.dates',
	'showitem' => 'datetime, starttime, endtime',
];
$GLOBALS['TCA'][$table]['palettes']['paletteEventPublish'] = [
	'label'    => $lll . 'tx_news_domain_model_news.palettes.publish',
	'showitem' => 'starttime, endtime',
];
$GLOBALS['TCA'][$table]['palettes']['paletteNewsAccess'] = [
	'label'    => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
	'showitem' => 'fe_group, --linebreak--, editlock',
];

$hasContentElements = str_contains((string)($GLOBALS['TCA'][$table]['types']['0']['showitem'] ?? ''), 'content_elements');

$head = 'title, --palette--;;paletteSlug, subtitle, teaser,';

$media = 'categories, fal_related_files, related_links,';

$sitemap = isset($GLOBALS['TCA'][$table]['palettes']['sitemap']) ? '--palette--;;sitemap,' : '';

$extraTabs = '
	--div--;frontend.tca:pages.tabs.metadata,
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;paletteAuthor,
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.metatags;metatags,
		--palette--;LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.palettes.alternativeTitles;alternativeTitles,
		' . $sitemap . '
	--div--;' . $lll . 'tabs.languageAccess,
		--palette--;;paletteLanguage,
		--palette--;;paletteHidden,
		--palette--;;paletteNewsAccess,
	--div--;core.form.tabs:notes,
		notes';

$GLOBALS['TCA'][$table]['types']['0']['showitem'] = '
	--div--;core.form.tabs:general,
		--palette--;;paletteCore,
		' . $head . '
		--palette--;;paletteNewsDates,
		fal_media,
		bodytext,
		' . ($hasContentElements ? 'content_elements,' : '') . '
		' . $media . '
	' . $extraTabs;

$GLOBALS['TCA'][$table]['types'][(string)$typeEvent] = [
	'wizardSteps' => [
		'basics' => [
			'title'  => 'LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:wizard.step.basics',
			'fields' => ['title', 'path_segment'],
		],
	],
	'columnsOverrides' => [
		'is_event' => ['config' => ['default' => 1]],
		'datetime' => ['label' => $lll . 'tx_news_domain_model_news.datetime.event'],
		// the event fields are only shown for is_event=1 by eventnews; here the type decides
		'full_day'         => ['displayCond' => 'FIELD:type:=:' . $typeEvent],
		'event_end'        => ['displayCond' => 'FIELD:type:=:' . $typeEvent],
		'organizer'        => ['displayCond' => 'FIELD:type:=:' . $typeEvent],
		'location'         => ['displayCond' => 'FIELD:type:=:' . $typeEvent],
		'organizer_simple' => ['displayCond' => 'FIELD:type:=:' . $typeEvent],
		'location_simple'  => ['displayCond' => 'FIELD:type:=:' . $typeEvent],
	],
	'showitem' => '
		--div--;core.form.tabs:general,
			--palette--;;paletteCore,
			' . $head . '
			--palette--;;paletteEventDates,
			--palette--;;paletteEventPlace,
			--palette--;;paletteEventPublish,
			fal_media,
			bodytext,
			' . $media . '
		' . $extraTabs,
];
