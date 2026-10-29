---
title: "Configuration reference"
order: 40
---

# Configuration reference

## The `consulting` tree

Dumped with Symfony's `YamlReferenceDumper` from
`Base\Consulting\DependencyInjection\ConsultingConfiguration`:

```yaml
consulting:
    # Where a quote request is sent.
    recipient:            '%env(MAILER_CONTACT)%'
    # Who provides the services, for schema.org (a name); null: the site's title.
    provider:             null
    regulated_activity_guard: false # Deprecated (Since omnibase/consulting 1.1: The "regulated_activity_guard" option is gone: a jurist's guard on legal advice is omnibase/legal's (legal.profession: jurist). Remove it.)
    authorized_status:    null # Deprecated (Since omnibase/consulting 1.1: The "authorized_status" option is gone: it is legal.jurist.authorized_status in omnibase/legal. Remove it.)
    # How long a quote request is kept, as the form's notice says.
    retention_months:     36
```

| Key | Type | Default | Read by |
|---|---|---|---|
| `recipient` | string | `%env(MAILER_CONTACT)%` | the quote page: the request's mail goes there, its reply-to the visitor |
| `provider` | string or null | null | `Service\JsonLd`: the `provider` of each `Service` and the name of the `OfferCatalog`; null takes the setting `base.settings.title` |
| `retention_months` | integer, at least 1 | 36 | the form's notice and `consulting:purge` |
| `regulated_activity_guard` | boolean | false | nothing: deprecated |
| `authorized_status` | string or null | null | nothing: deprecated |

The two deprecated keys are refused when set: `regulated_activity_guard: true`
fails with "consulting.regulated_activity_guard: the guard on what the law of
31 December 1971 reserves moved to omnibase/legal - install it with
legal.profession: jurist, and remove this option.", and any
`authorized_status` fails with "consulting.authorized_status moved to
omnibase/legal: legal.jurist.authorized_status.".

## Parameters

Each key is also a container parameter: `%consulting.recipient%`,
`%consulting.provider%`, `%consulting.retention_months%`.

## Services

`config/services.php` autowires and autoconfigures everything under
`Base\Consulting\` except the DI classes, entities, enums, exceptions, form
models and `Offering\OfferingViolation`. The services a site meets:

| Service | Role |
|---|---|
| `Base\Consulting\Repository\OfferingRepository` | `findVisible(?int $limit)`, `findOneVisible(string $slug)` |
| `Base\Consulting\Repository\QuoteRequestRepository` | `findNew(?int $limit)`, `countNew()`, `purgeOlderThan(\DateTimeImmutable $before)` |
| `Base\Consulting\Service\JsonLd` | `service(Offering)`, `catalog(list<Offering>)` |
| `Base\Consulting\Service\CreditBridge` | `isAvailable()`, `canSell(Offering)` |
| `Base\Consulting\Validator\OfferingRulesValidator` | asks every `consulting.offering_rule` service |
| `Base\Consulting\Twig\ConsultingExtension` | `consulting_offerings(limit)`, `consulting_can_pay(offering)` |
| `Base\Consulting\Command\PurgeCommand` | `consulting:purge` |

`consulting_offerings()` returns an empty list when the database cannot be
read (a page that includes it still renders before the migration ran).

## Translations

Domain `consulting` (`activity`, `offerings`, `offering`, `quote`, `admin`) and
domain `validators` (`quote.consent_required`), in English, French and German,
ICU message format. A site overrides a message in its own
`translations/consulting+intl-icu.<locale>.yaml`.
