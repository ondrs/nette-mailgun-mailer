<?php

namespace ondrs\MailgunMailer;

use Mailgun\Mailgun;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\MimePart;

class Mailer implements IMailer
{

    /** @var Mailgun */
    private $mailgun;

    /** @var  Options */
    private $options;

    /** @var SendResponse|NULL */
    private $lastResponse;


    public function __construct(array $options)
    {
        $this->options = new Options($options);
        $this->mailgun = Mailgun::create($this->options->apiKey);
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

        // inlines are private and this seems to be the best way how to get them
        $ref = new \ReflectionObject($msg);
        $inlines = $ref->getProperty('inlines');
        $inlines->setAccessible(TRUE);

        $msg->generateMessage();
        $headers = $msg->getHeaders();

        $htmlBody = $msg->getHtmlBody();

        /**
         * @var string   $filePath
         * @var MimePart $mimePart
         */
        foreach ($inlines->getValue($msg) as $filePath => $mimePart) {

            if (!preg_match('/<(.*?)>/', $mimePart->getHeader('Content-ID'), $cidMatches)) {
                throw new LogicException('Unable match get Content-ID');
            }

            $htmlBody = str_replace($cidMatches[1], basename($filePath), $htmlBody);
        }

        // last response is intently stored in the property bcs the interface's return type is void
        $this->lastResponse = $this->mailgun->messages()
            ->send($this->options->domain, [
                'from' => self::parseHeader($headers, 'From'),
                'to' => self::parseHeader($headers, 'To'),
                'cc' => self::parseHeader($headers, 'Cc'),
                'bcc' => self::parseHeader($headers, 'Bcc'),
                'subject' => $msg->getSubject(),
                'html' => $htmlBody,
                'text' => $msg->getBody(),
                'attachment' => self::createAttachments($msg->getAttachments()),
                'inline' => self::createAttachments($inlines->getValue($msg)),
            ]);
    }


    /**
     * @param array  $header
     * @param string $key
     * @return NULL|string
     */
    public static function parseHeader(array $header, $key)
    {
        if (!array_key_exists($key, $header)) {
            return NULL;
        }

        if (is_array($header[$key])) {
            $s = '';
            foreach ((array)$header[$key] as $email => $name) {
                if ($name != NULL) { // intentionally ==
                    $s .= $name;
                    $email = " <$email>";
                }
                $s .= $email . ',';
            }

            return rtrim($s, ','); // last comma
        }

        return $header[$key];
    }


    /**
     * @param MimePart[] $mimeParts
     * @return array
     * @throws \ondrs\MailgunMailer\LogicException
     */
    public static function createAttachments(array $mimeParts)
    {
        $arr = [];

        foreach ($mimeParts as $possiblePath => $mimePart) {
            if (!preg_match('/filename="(.*?)"/', $mimePart->getHeader('Content-Disposition'), $nameMatches)) {
                throw new LogicException('Unable to get a filename from the Content-Disposition header');
            }

            if (is_file($possiblePath)) {   // this is inline

                $arr[] = [
                    'filename' => $nameMatches[1],
                    'filePath' => $possiblePath,
                ];

            } else {
                $arr[] = [
                    'filename' => $nameMatches[1],
                    'fileContent' => $mimePart->getBody(),
                ];
            }
        }

        return $arr;
    }
}