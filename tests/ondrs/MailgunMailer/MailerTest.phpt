<?php

use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class MailerTest extends Tester\TestCase
{

    /** @var  \ondrs\MailgunMailer\Mailer */
    private $mailer;

    /** @var array  */
    private $options = [];

    private $html = <<<HTML
<h1>HEADER</h1>
<p>Hello, I am testing email body</p>
<ul>
<li>1</li>
<li>2</li>
<li>3</li>
</ul>
HTML;


    function setUp()
    {
        $optionsFile = __DIR__ . '/../../options.php';
        $this->options = is_file($optionsFile)
            ? require $optionsFile
            : require __DIR__ . '/../../options.env.php';

        $this->mailer = new \ondrs\MailgunMailer\Mailer($this->options['domain'], \Mailgun\Mailgun::create($this->options['apiKey']));
    }


    function testSimpleEmail()
    {
        $message = new \Nette\Mail\Message();
        $message->setBody('simple email body');
        $message->setSubject('simple email');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);

        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testSimpleEmailWithReply()
    {
        $message = new \Nette\Mail\Message();
        $message->setBody('simple email body');
        $message->setSubject('simple email with reploy');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addReplyTo('reply@email.com', 'John Reply');
        $message->addTo($this->options['to']);

        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testSimpleEmailWithCcAndBcc()
    {
        $message = new \Nette\Mail\Message();
        $message->setBody('simple email body');
        $message->setSubject('simple email with cc and bcs');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);

        $message->addCc($this->options['to'], 'cc1');
        $message->addBcc($this->options['to'], 'bcc1');

        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testHtmlEmail()
    {
        $message = new \Nette\Mail\Message();
        $message->setHtmlBody($this->html);
        $message->setSubject('html email');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);

        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testHtmlEmailWithSingleAttachement()
    {
        $message = new \Nette\Mail\Message();
        $message->setHtmlBody($this->html);
        $message->setSubject('html email with single attachment');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);

        $message->addAttachment(__DIR__ . '/data/test-file.txt');

        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testHtmlEmailWithMultipleAttachements()
    {
        $message = new \Nette\Mail\Message();
        $message->setHtmlBody($this->html);
        $message->setSubject('html email with multiple attachments');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);

        $message->addAttachment(__DIR__ . '/data/test-file.txt');
        $message->addAttachment(__DIR__ . '/data/test-image.jpg');

        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testHtmlEmailWithSingleInline()
    {
        $html = $this->html . '<hr><img src="data/test-image.jpg">';

        $message = new \Nette\Mail\Message();
        $message->setHtmlBody($html, __DIR__);
        $message->setSubject('html email with single inline');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);
        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


    function testHtmlEmailWithMultipleInlines()
    {
        $html = $this->html . '<hr><img src="data/test-image.jpg">';
        $html .= '<hr><img src="data/3.jpg">';

        $message = new \Nette\Mail\Message();
        $message->setHtmlBody($html, __DIR__);
        $message->setSubject('html email with multiple inlines');
        $message->setFrom($this->options['from'], 'mailgun mailer test');
        $message->addTo($this->options['to']);
        $this->mailer->send($message);

        Assert::type(\Mailgun\Model\Message\SendResponse::class, $this->mailer->getLastResponse());
    }


}


run(new MailerTest());
