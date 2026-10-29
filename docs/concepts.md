---
title: "Concepts"
order: 20
---

# Concepts

## An offering

An `Offering` is one service as the site prints it. Its title gives its slug
(ASCII, lower case, at most 120 characters) unless a slug is set; its
`activity` is one case of `Base\Consulting\Enum\Activity`:

| Case | Value |
|---|---|
| `TRAINING` | `training` (the default) |
| `TALK` | `talk` |
| `CONSULTING` | `consulting` |
| `INFORMATION` | `information` |
| `WRITING` | `writing` |
| `OTHER` | `other` |

`setActivity()` takes a case or its value; an unknown value becomes `OTHER`.
The price is a string printed as written ("On quote", "450 € the day"), not an
amount: the bundle sells nothing. `hours` is the size of a package of hours,
read only when omnibase/marketplace can sell one.

The description is the back office's editor output or plain text:
`isHtml()` says which (it looks for `p`, `ul`, `ol`, `h1`-`h6`, `div`, `br`,
`strong`, `em`, `a` tags), and the page prints one or the other. `getText()`
joins the title, the summary and the description without tags: it is what the
offering rules read.

An offering appears on the site when `visible` is true, in the order of
`position`. `quotable` says whether the quote form lists it.

## A quote request

A `QuoteRequest` is what a visitor sent through `/quote`: name, e-mail, phone,
organisation, number of participants, dates, location, budget, message, the
locale of the page, the offering it named (set to null when that offering is
deleted) and the moment it was sent. Its status is a case of
`Base\Consulting\Enum\QuoteStatus`: `new`, `answered`, `archived`. The back
office adds its own notes.

The form does not write the entity: it fills a model,
`Base\Consulting\Form\Model\QuoteRequestModel` (omnisense/omnibase's
`ContactModel` with the quote's fields and the consent), and
`toQuoteRequest($locale)` builds the entity once the form is valid. A robot
refused by the guard leaves nothing in the database.

This example builds an offering and a request from its model, outside any
host:

```php
use Base\Consulting\Entity\Offering;
use Base\Consulting\Enum\Activity;
use Base\Consulting\Form\Model\QuoteRequestModel;

$offering = (new Offering('Atelier : enseigner l’allemand au CP', Activity::TRAINING))
    ->setSummary('Une demi-journée pour les enseignants.')
    ->setDescription('<p>Jeux, chansons et <strong>rituels</strong>.</p>')
    ->setActivity('legal_consultation');
echo $offering->getSlug(), ' | ', $offering->getActivity()->value, ' | html: ', var_export($offering->isHtml(), true), "\n";
echo str_replace("\n", ' / ', $offering->getText()), "\n";

$model = new QuoteRequestModel();
$model->name = ' Anna Keller ';
$model->email = 'anna@example.org';
$model->message = 'Pour 30 enseignants, en mars.';
$model->offering = $offering;
$model->participants = '30';
$quote = $model->toQuoteRequest('fr_FR');
echo $quote, ' | ', $quote->getStatus()->value, ' | ', $quote->getLocale(), ' | ', $quote->getOffering()?->getTitle(), "\n";
```

Its output, run in omnisense/omnibase-harness:

```text
atelier-enseigner-l-allemand-au-cp | other | html: true
Atelier : enseigner l’allemand au CP / Une demi-journée pour les enseignants. / Jeux, chansons et rituels.
Anna Keller | new | fr_FR | Atelier : enseigner l’allemand au CP
```

The activity `legal_consultation` is not one of the cases, so the offering is
`other`; the name is trimmed; a new request is `new`.

## Keeping and forgetting

The notice under the form says what is kept, why, for how long
(`consulting.retention_months`, 36 by default) and how to have it removed.
`consulting:purge` deletes the requests sent before that many months ago; it
runs from cron, once a day ([Installation and configuration](installation.md)).
