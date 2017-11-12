<?php

namespace ondrs\MailgunMailer;

use Mailgun\Mailgun;
use Nette\Mail\IMailer;
use Nette\Mail\Message;

class Mailer implements IMailer
{

    /** @var string */
    private $domain;

    /** @var Mailgun */
    private $mailgun;

    /** @var SendResponse|NULL */
    private $lastResponse;


    /**
     * @param string  $domain
     * @param Mailgun $mailgun
     */
    public function __construct($domain, Mailgun $mailgun)
    {
        $this->domain = $domain;
        $this->mailgun = $mailgun;
    }


    /**
     * @return SendResponse|NULL
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }


    /**
     * @inheritdoc
     * @throws \ondrs\MailgunMailer\LogicException
     * @throws \Exception
     */
    public function send(Message $message)
    {
        $msg = clone $message;

        $msg->generateMessage();

        // last response is intently stored in the property bcs the interface's return type is void
        $this->lastResponse = $this->mailgun->messages()
            ->send($this->domain, formatParams($msg));
    }

}