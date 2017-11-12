<?php

use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function() {

    $message = new \Nette\Mail\Message();
    $message->setBody('email body');
    $message->setSubject('email subject');
    $message->setFrom('from@email.com', 'mailgun mailer test');
    $message->addTo('to@email.com', 'Bob To');
    $message->addTo('to2@email.com');
    $message->addReplyTo('reply@email.com', 'John Reply');

    $params = \ondrs\MailgunMailer\formatParams($message);

    Assert::type('array', $params);


    Assert::equal('email subject', $params['subject']);
    Assert::equal('email body', $params['text']);
    Assert::equal('mailgun mailer test <from@email.com>', $params['from']);
    Assert::equal('Bob To <to@email.com>,to2@email.com', $params['to']);
    Assert::equal('John Reply <reply@email.com>', $params['h:Reply-To']);

    Assert::null($params['cc']);
    Assert::null($params['bcc']);

    Assert::count(0, $params['attachment']);
    Assert::count(0, $params['inline']);


});