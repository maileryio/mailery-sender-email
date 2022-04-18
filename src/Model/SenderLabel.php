<?php

namespace Mailery\Sender\Email\Model;

class SenderLabel
{
    /**
     * @param string|null $name
     * @param string|null $email
     */
    public function __construct(
        private ?string $name = null,
        private ?string $email = null
    ) {}

    /**
     * @return string
     */
    public function __toString()
    {
        if (empty($this->name)) {
            return $this->email;
        }

        return sprintf('%s <%s>', $this->name, $this->email);
    }

    /**
     * @param string $string
     * @return self[]
     */
    public static function fromString(string $string): array
    {
        $emails = [];

        if(preg_match_all('/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/', $string, $matches, PREG_SET_ORDER) > 0) {
            foreach($matches as $m) {
                if(!empty($m[2])) {
                    $emails[trim($m[2], '<>')] = $m[1];
                } else {
                    $emails[$m[1]] = '';
                }
            }
        }

        $results = [];

        foreach ($emails as $email => $name) {
            $results[] = new self($name, $email);
        }

        return $results;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
