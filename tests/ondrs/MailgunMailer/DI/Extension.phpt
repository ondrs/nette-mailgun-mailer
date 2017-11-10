<?php

use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

test(function () {
    $configurator = new Nette\Configurator;
    $configurator->setTempDirectory(TEMP_DIR);
    $configurator->addConfig(__DIR__ . '/config.neon');
    $container = $configurator->createContainer();

    $mailer = $container->getByType(\Nette\Mail\IMailer::class);

    Assert::type(\ondrs\MailgunMailer\Mailer::class, $mailer);
});


test(function () {
    $configurator = new Nette\Configurator;
    $configurator->setTempDirectory(TEMP_DIR);
    $configurator->addConfig(__DIR__ . '/config.smtp.neon');
    $container = $configurator->createContainer();

    $mailer = $container->getByType(\Nette\Mail\IMailer::class);

    Assert::type(\ondrs\MailgunMailer\Mailer::class, $mailer);
});