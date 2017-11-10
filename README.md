Nette Mailgun Mailer [![Build Status](https://travis-ci.org/ondrs/nette-mailgun-mailer.svg?branch=master)](https://travis-ci.org/ondrs/nette-mailgun-mailer)
==============

Implementation of Nette\IMailer for Mailgun PHP SDK.

Installation
-----

composer.json

    "ondrs/nette-mailgun-mailer": "v0.1.0"

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