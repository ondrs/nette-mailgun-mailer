<?php

namespace ondrs\MailgunMailer\DI;

use Nette\DI\CompilerExtension;
use ondrs\MailgunMailer\Mailer;

class Extension extends CompilerExtension
{

    /** @var array */
    private $defaults = [
        'apiKey' => NULL,
        'domain' => NULL,
    ];


    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        $mailer = $builder->addDefinition($this->prefix('mailer'))
            ->setClass(\Nette\Mail\IMailer::class);

        $mailer->setFactory(Mailer::class, [$config]);

        if ($this->name === 'mail') {
            $builder->addAlias('nette.mailer', $this->prefix('mailer'));
        }
    }

}
