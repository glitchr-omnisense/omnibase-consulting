---
title: "The back office"
order: 36
---

# The back office

The back office screens are loaded when omnibase/admin is installed (its
`Base\Admin\Controller\AbstractCrudController` exists). They carry
omnibase/admin's `#[OpenToAdmins]`, so the site's administrator (`ROLE_ADMIN`)
writes in them, not the super-admin only; any other user is refused.

## Offerings

`Base\Consulting\Controller\Admin\Crud\OfferingCrudController` creates, edits
and deletes an offering. Its fields, in order: title, activity (the enum's
cases, translated), slug (following the title), summary, description (the
editor), audience, format, duration, price (with a help text: it is printed as
written), hours (with a help text: read once the marketplace sells hours),
languages, image, position, quotable, visible.

## Quote requests

`Base\Consulting\Controller\Admin\Crud\QuoteRequestCrudController` reads the
requests. There is no "new" action: a request comes from the site's form. What
the visitor wrote (date, name, e-mail, phone, organisation, participants,
dates, location, budget, message) is shown disabled; the status, the offering
and the notes can be changed.

Two actions, on the index and on the detail page, set the status:

| Action | Path under the screen | Status set |
|---|---|---|
| `answered` | `/{entityId}/answered` | `answered` |
| `archive` | `/{entityId}/archive` | `archived` |

Both are open to the administrator (`#[OpenToAdmins(actions: ['answered',
'archive'])]`). Each flashes `admin.quote.flash.<status>` (domain
`consulting`, with the visitor's name) and goes back to the page it came from
when the referer is on the same host, to the index otherwise.

## The dashboard tile

`Base\Consulting\Admin\Widget\QuoteRequestsWidgetType` is the widget
`consulting_quotes`: the number of new requests (`countNew()`) and the five
latest (`findNew(5)`), rendered by
`@Consulting/admin/widget/quotes.html.twig`. A dashboard adds it as a block:

```php
yield MenuItem::block('consulting_quotes', 'Demandes de devis', 'fa-solid fa-file-signature')->setSize(2);
```
