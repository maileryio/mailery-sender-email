<?php

namespace Mailery\Sender\Email\Model;

use Mailery\Sender\Model\SenderTypeInterface;

class EmailType implements SenderTypeInterface
{
    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'Email address';
    }

    /**
     * @return string
     */
    public function getCreateLabel(): string
    {
        return 'Email address';
    }

    /**
     * @return string|null
     */
    public function getCreateRouteName(): ?string
    {
        return '/sender/email/create';
    }

    /**
     * @return array
     */
    public function getCreateRouteParams(): array
    {
        return [];
    }
}
