<?php

namespace Mailery\Sender\Email\Model;

class VerificationToken
{
    /**
     * @var int
     */
    private int $length = 10;

    /**
     * @param int $length
     * @return self
     */
    public function withLength(int $length): self
    {
        $new = clone $this;
        $new->length = $length;

        return $new;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $this->length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
