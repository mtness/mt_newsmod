# MT Newsmod (`mt_newsmod`)

One-page backend layouts for [EXT:news](https://github.com/georgringer/news) with the
[EXT:eventnews](https://github.com/georgringer/eventnews) event fields built in.

## What it does

- **News (type 0):** one main tab (type, title, slug, subtitle, teaser, dates incl. publish / expiration,
  media, text, content elements, categories, related files and links) plus the tabs Metadata,
  Language & access and Notes.
- **Event (type 3):** same tabs, but the event fields sit together below the teaser:
  *When* (start, end, full day), *Where / who* (location, organizer and their simple variants) and *Publishing*.
- **Subtitle:** new column `tx_news_domain_model_news.subtitle`.
- The type 1 / 2 (internal / external link) layouts only lose eventnews' event tab and `is_event` switch.
- The "preview" button of the content elements field is removed.
- `is_event` is derived from the type on every save (`Hooks/NewsEventTypeHook`), because the
  eventnews frontend queries only know that flag.

## Migration

Existing events (`is_event = 1`) become type 3 with the upgrade wizard (safe to run repeatedly):

```
vendor/bin/typo3 upgrade:run mtNewsmodEventType
```

## Optional page TSconfig (per site)

Create new records as events in an events folder:

```
[traverse(page, "uid") == {$storagePids.events}]
	TCAdefaults.tx_news_domain_model_news.type = 3
[end]
```

Fields hidden for editors (`author_email`, `keywords`, `tags`, `related`, ...) are not part of the layouts.
