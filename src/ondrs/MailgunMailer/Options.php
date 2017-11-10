<?php

namespace ondrs\MailgunMailer;

/**
 * @property-read string apiKey
 * @property-read string domain
 */
class Options
{

    private static $requiredKeys = [
        'apiKey',
        'domain',
    ];


    public function __construct(array $options)
    {
        if (count(array_intersect(self::$requiredKeys, array_keys($options))) !== count(self::$requiredKeys)) {
            throw new InvalidArgumentException('Not all of the required options are configured: ' . implode(', ', self::$requiredKeys));
        }

        foreach (self::$requiredKeys as $key) {
            if (array_key_exists($key, $options)) {
                $this->$key = $options[$key];
            }

        }
    }

}