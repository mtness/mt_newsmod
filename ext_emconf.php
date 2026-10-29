<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'MT Newsmod',
	'description' => 'One-page backend layouts for EXT:news and an event record type (type 3) for EXT:eventnews, plus a subtitle field.',
	'category' => 'be',
	'author' => 'Markus Timtner',
	'author_email' => 'markus@timtner.tech',
	'author_company' => 'https://timtner.tech',
	'version' => '14.0.0',
	'state' => 'stable',
	'constraints' => [
		'depends' => [
			'php' => '8.2.0-8.5.99',
			'typo3' => '14.3.0-14.9.99',
			'news' => '14.0.0-14.99.99',
			'eventnews' => '7.0.0-7.99.99',
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
