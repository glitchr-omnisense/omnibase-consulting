---
title: "Installation and configuration"
order: 10
---

# Installation and configuration

```php
// config/bundles.php
Base\Consulting\ConsultingBundle::class => ['all' => true],
```

```yaml
# config/routes.yaml
consulting_controller:
    resource: "@ConsultingBundle/src/Controller/Client"
    type: attribute
    prefix: /
```

```yaml
# config/packages/consulting.yaml (every key optional)
consulting:
    recipient: '%env(MAILER_CONTACT)%'   # where a quote request is sent
    provider: ~                           # who provides the services (schema.org); ~: base.settings.title
    retention_months: 36                  # how long a request is kept (the notice says so)
```

```bash
bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate   # consulting_offering, consulting_quote_request
bin/console assets:install                                                       # public/css/consulting.css
```

```cron
10 4 * * *  php /srv/app/bin/console consulting:purge >> /srv/app/var/log/cron.consulting-purge.log 2>&1
```

```php
// DashboardController
yield MenuItem::block('consulting_quotes', 'Demandes de devis', 'fa-solid fa-file-signature')->setSize(2);
yield MenuItem::linkToCrud(\Base\Consulting\Entity\Offering::class, 'Prestations', 'fa-solid fa-handshake');
yield MenuItem::linkToCrud(\Base\Consulting\Entity\QuoteRequest::class, 'Demandes de devis', 'fa-solid fa-file-signature');
```

The templates extend `layout1.html.twig` (`title`, `description`,
`stylesheets`, `content`); `consulting.css` follows `--consulting-accent`,
`--consulting-on-accent`, `--consulting-ink`, `--consulting-soft`,
`--consulting-line`, `--consulting-surface`, `--consulting-font-display`,
`--consulting-measure`.

## Requirements

- PHP 8.2 or later;
- doctrine/orm 2.15 or 3;
- omnisense/omnibase 3.0 or 4.0, omnibridge/symfony 1.x, omnibase/admin;
- Symfony 6.4, 7 or 8 (config, dependency-injection, form, http-kernel,
  mailer, string, validator);
- a mailer DSN, and `MAILER_CONTACT` (or `consulting.recipient`) for the
  address the requests go to.

Suggested: omnibase/marketplace, for hours paid in advance once it has
`Credit` ([Hours paid in advance](credits.md)); omnisense/omnishield, for the
captcha of the quote form.
