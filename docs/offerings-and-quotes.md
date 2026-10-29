---
title: "Offerings and quote requests"
order: 30
---

# Offerings and quote requests

## Offering

`title`, `slug`, `activity` (`Enum\Activity`: training, talk, consulting,
information, writing, other), `summary`,
`description` (the back office's editor, or plain text), `audience`,
`format`, `duration`, `price` (as printed: "On quote"), `hours` (a package
sold by the hour, once the marketplace does), `languages`, `image`,
`quotable`, `position`, `visible`.

Twig: `consulting_offerings(n)` for a home page,
`consulting_can_pay(offering)`. `{% include '@Consulting/client/_offering.html.twig' %}`
renders a card. JSON-LD: a `Service` per offering, an `OfferCatalog` on
`/services`.

## The quote request

`Form\Type\QuoteRequestType` is omnibase's `Base\Form\Type\ContactType`
(name, e-mail, phone, message) with the offering, the organisation, the
participants, the dates, the place, the budget, and the consent to the
notice; its model `Form\Model\QuoteRequestModel` extends
`Base\Form\Model\ContactModel`.

It is guarded as omnisense/omnibase guards a form - the option `guard`
(`action: quote`; omnisense/omnibase's `docs/20-architecture/guard.md`): a
trap, the time it takes (a signed stamp), the lists of `base.guard.reputation`,
the captcha when the site has omnisense/omnishield. Without omnishield, the trap
and the time alone. A robot is refused on the form, before anything is kept.

```php
$form = $forms->createNamed('quote', QuoteRequestType::class, $model = new QuoteRequestModel());
if ($form->isSubmitted() && $form->isValid()) {
    $em->persist($model->toQuoteRequest($request->getLocale()));
}
```

The page keeps the request (`QuoteRequest`: new, answered, archived, the
back office's notes) and mails it to `consulting.recipient`, its reply-to
the visitor. The notice under the form says what is kept, why, for how long
(`retention_months`) and how to have it removed; `consulting:purge` keeps
that promise.

## Who writes in the back office

The two screens (`OfferingCrudController`, `QuoteRequestCrudController`) are
the site's administrator's (`ROLE_ADMIN`: the professional whose site it
is), not the super-admin's only: they carry omnibase/admin's
`#[OpenToAdmins]` - creating, editing and deleting a service; annotating,
deleting a quote request and its two buttons, `answered` and `archive`. The
attribute is omnibase/admin's from 7474f85.

## The pages

| Route | Path | Methods | Template | What it does |
|---|---|---|---|---|
| `consulting_offerings` | `/services` | GET | `@Consulting/client/offerings.html.twig` | the visible offerings by position, with an `OfferCatalog`; in the sitemap (priority 0.8, monthly) |
| `consulting_offering` | `/services/{slug}` | GET | `@Consulting/client/offering.html.twig` | one visible offering (slug `[a-z0-9-]+`), its `Service`, the other offerings; 404 for an unknown or hidden slug |
| `consulting_quote` | `/quote` | GET, POST | `@Consulting/client/quote.html.twig` | the form; `?offering=<slug>` selects a visible offering |

The quote page answers 200 with an empty form, 422 when a submitted form is
invalid (a guard's refusal included), and, once valid, keeps the request,
sends the mail and renders `@Consulting/client/quote_sent.html.twig` with the
model. The mail's subject is `quote.mail.subject` (domain `consulting`, with
the visitor's name and the offering's title, or "—"), its body
`@Consulting/email/quote.html.twig` with the request.

The routes are imported from the controller's directory
([Installation and configuration](installation.md)); a site that wants other
paths imports them with a prefix or declares its own routes to the same
controller.

## Overriding the templates

The templates are Twig's `@Consulting` namespace: a site overrides one in
`templates/bundles/ConsultingBundle/` (for instance
`templates/bundles/ConsultingBundle/client/_offering.html.twig`). The partial
`@Consulting/client/_jsonld.html.twig` prints the JSON-LD a page passes as
`jsonld`.
