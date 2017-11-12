Nette Mailgun Mailer [![Total Downloads](https://poser.pugx.org/ondrs/nette-mailgun-mailer/downloads)](https://packagist.org/packages/ondrs/nette-mailgun-mailer) [![Build Status](https://travis-ci.org/ondrs/nette-mailgun-mailer.svg?branch=master)](https://travis-ci.org/ondrs/nette-mailgun-mailer) [![Latest Stable Version](https://poser.pugx.org/ondrs/nette-mailgun-mailer/v/stable)](https://packagist.org/packages/ondrs/nette-mailgun-mailer)
==============

Implementation of Nette\IMailer for Mailgun PHP SDK.

Installation
-----

composer.json

    "ondrs/nette-mailgun-mailer": "v0.2.1"

Configuration
-----

Register the extension:

```yaml
extensions:
  mailgun: ondrs\MailgunMailer\DI\Extension
```

And configure it:

```yaml
mailgun:
  apiKey: 'testing-api-key'
  domain: 'domain.com'
```

Why?
-----
Implementation of Nette\SmtpMailer is [broken and nobody cares](https://github.com/nette/mail/pull/40).
Correct definition of email headers is tricky and can change over time.
Sending emails directly via Mailgun API solves this issue. 
Mailgun service should generate (hopefully) correct headers for us.


*This extension overrides default mailer service definition.*
