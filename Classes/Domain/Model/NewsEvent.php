<?php
declare(strict_types=1);

namespace MarkusTimtner\MtNewsmod\Domain\Model;

use GeorgRinger\News\Domain\Model\News;

/**
 * News of record type 3 (event). EXT:news merges the eventnews fields into
 * the parent class, so this class only gives Extbase a model for the type.
 */
class NewsEvent extends News {}
