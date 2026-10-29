<?php

defined('TYPO3') or die();

// keep eventnews' is_event in line with the news type "event"
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mt_newsmod']
	= \MarkusTimtner\MtNewsmod\Hooks\NewsEventTypeHook::class;
