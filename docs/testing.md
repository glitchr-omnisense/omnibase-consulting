---
title: "Testing"
order: 60
---

# Testing

## Running the suite

From the package's checkout, with its dev dependencies:

```bash
vendor/bin/phpunit
```

Inside a host application:

```bash
php vendor/bin/phpunit -c vendor/omnibase/consulting/phpunit.xml.dist
```

Or in omnisense/omnibase-harness, mounting the checkout in place of the
installed package:

```bash
docker run --rm --network none \
    -v "$PWD":/srv/demo/vendor/omnibase/consulting:ro \
    omnisense/omnibase-harness:latest test consulting
```

On this checkout, in the harness:

```text
Tests: 25, Assertions: 89, Skipped: 1.
```

The skipped test is the captcha's: the harness asks the captcha only after
refused tries (`base.guard.captcha_after`), so a first request without its
token is not refused there.

## What the tests check

| Test | Checks |
|---|---|
| `Controller\Admin\OpenToAdminsTest` | the administrator writes in both screens; any other user is refused (needs a host kernel and an omnibase/admin with `#[OpenToAdmins]`) |
| `Controller\CrudRequiredFieldsTest` | omnibase/admin's `CrudFieldsTestCase` on the two screens: a field is required exactly when the record needs it, an optional field takes an empty value, one field per property, the slug follows a field |
| `Entity\EnumFieldsTest` | an offering holds its activity, a request its status; the columns are strings of the enum's length; each case names itself in the `consulting` domain |
| `Form\QuoteRequestGuardTest` | the form is guarded in place of its own trap; a request sent as a person goes through; a filled trap, a request sent too fast, a request without the captcha's token (when there is one) are refused; without a captcha, the trap and the time alone |
| `Service\CreditBridgeTest` | no hours are sold before the marketplace has `Credit` |
| `Validator\OfferingRulesTest` | without rules every offering validates; a rule refuses under the field it names; every rule is asked; no activity is reserved here |

A test that needs a host (its kernel) is skipped when run from a bare
checkout.
