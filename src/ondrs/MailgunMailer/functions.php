<?php

namespace ondrs\MailgunMailer;

use Nette\Mail\Message;
use Nette\Mail\MimePart;


/**
 * @param Message $message
 * @return MimePart[]
 */
function extractInlines(Message $message)
{
    $msg = clone $message;

    // inlines are private and this seems to be the best way how to get them
    $ref = new \ReflectionObject($msg);
    $inlines = $ref->getProperty('inlines');
    $inlines->setAccessible(TRUE);

    return $inlines->getValue($msg);
}


/**
 * @param Message    $message
 * @param MimePart[] $inlines
 * @return string
 * @throws \ondrs\MailgunMailer\LogicException
 */
function formatHtmlBody(Message $message, array $inlines)
{
    $htmlBody = $message->getHtmlBody();

    /**
     * @var string   $filePath
     * @var MimePart $mimePart
     */
    foreach ($inlines as $filePath => $mimePart) {
        if (!preg_match('/<(.*?)>/', $mimePart->getHeader('Content-ID'), $cidMatches)) {
            throw new LogicException('Unable match Content-ID');
        }

        $htmlBody = str_replace($cidMatches[1], basename($filePath), $htmlBody);
    }

    return $htmlBody;
}


/**
 * @param Message    $message
 * @return array
 * @throws \ondrs\MailgunMailer\LogicException
 */
function formatParams(Message $message)
{
    $inlines = extractInlines($message);

    $data = [
        'from' => $message->getEncodedHeader('From'),
        'to' => $message->getEncodedHeader('To'),
        'cc' => $message->getEncodedHeader('Cc'),
        'bcc' => $message->getEncodedHeader('Bcc'),
        'subject' => $message->getSubject(),
        'html' => formatHtmlBody($message, $inlines),
        'text' => $message->getBody(),
        'attachment' => createAttachments($message->getAttachments()),
        'inline' => createAttachments($inlines),
    ];

    $extraHeaders = ['Reply-To', 'Return-Path'];

    foreach ($extraHeaders as $hdr) {
        if ($encodedHdr = $message->getEncodedHeader($hdr)) {
            $data["h:$hdr"] = $encodedHdr;
        }
    }

    return $data;
}


/**
 * @param MimePart[] $mimeParts
 * @return array
 * @throws \ondrs\MailgunMailer\LogicException
 */
function createAttachments(array $mimeParts)
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