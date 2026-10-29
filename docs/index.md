# omnibase/consulting

omnibase/consulting is the services page of a trainer, a speaker, a consultant
or a writer: what is offered, a page per service, and a quote request that is
kept for the back office and sent to the site's address. It is a Symfony bundle
of the omnibase family (namespace `Base\Consulting\`), built on
omnisense/omnibase and omnibase/admin.

## What it adds

- Two entities: `Offering` (`consulting_offering`), a service with its
  activity, summary, description, audience, format, duration, price and
  picture; `QuoteRequest` (`consulting_quote_request`), what a visitor asked,
  with its status and the back office's notes.
- Three routes: `/services` (the list, in the sitemap), `/services/{slug}` (a
  service) and `/quote` (the request form, `?offering=<slug>` to name the
  service).
- The quote form: omnisense/omnibase's contact form with what a quote needs,
  guarded (trap, timing, captcha when omnisense/omnishield is installed) and
  closed by a consent to the notice.
- A tag, `consulting.offering_rule`, for the rules an offering keeps: the bundle
  refuses nothing by itself, a site or a profession package adds its rules.
- Two back office screens (offerings, quote requests) open to the site's
  administrator, and a dashboard tile counting the new requests.
- `consulting:purge`, which deletes the requests older than
  `consulting.retention_months`, as the form's notice promises.
- schema.org JSON-LD: a `Service` per offering, an `OfferCatalog` on
  `/services`.
- Two Twig functions, `consulting_offerings()` and `consulting_can_pay()`.
- Translations in English, French and German (domains `consulting` and
  `validators`).

## What it leaves to others

Hours paid in advance belong to omnibase/marketplace's generic `Credit`: until
that class exists, no offering is sold by the hour ([Hours paid in
advance](credits.md)). A jurist's guard on legal advice is omnibase/legal's
(`legal.profession: jurist`); the two former options that held it here are
deprecated and refused when set ([Configuration reference](configuration.md)).

## Pages

- [Installation and configuration](installation.md)
- [Concepts](concepts.md)
- [Offerings and quote requests](offerings-and-quotes.md)
- [Rules an offering keeps](offering-rules.md)
- [Hours paid in advance](credits.md)
- [The back office](back-office.md)
- [Configuration reference](configuration.md)
- [Testing](testing.md)
