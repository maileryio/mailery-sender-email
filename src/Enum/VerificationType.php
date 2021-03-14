<?php

namespace Mailery\Sender\Email\Enum;

use Mailery\Sender\Email\Entity\EmailSender;

class VerificationType
{
    public const DOMAIN = 'domain';
    public const TOKEN = 'token';

    /**
     * @return array
     */
    public function getLabels() : array
    {
        return [
            self::DOMAIN => 'Domain verification',
            self::TOKEN => 'Email confirmation',
        ];
    }

    /**
     * @param string $type
     * @return string|null
     */
    public function getLabel(string $type): ?string
    {
        return $this->getLabels()[$type] ?? null;
    }

    /**
     * @param EmailSender $sender
     * @return string|null
     */
    public function getLabelBySender(EmailSender $sender): ?string
    {
        if (($type = $sender->getVerificationType()) === null) {
            return null;
        }
        return $this->getLabel($type);
    }
}
