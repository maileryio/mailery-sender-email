<?php

namespace Mailery\Sender\Email\Entity\Embedded;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Column;
use Mailery\Sender\Email\Field\VerificationType;

#[Embeddable]
class Verification
{

    #[Column(type: 'string(255)', typecast: VerificationType::class, nullable: true)]
    private ?VerificationType $type = null;

    #[Column(type: 'string(255)', nullable: true)]
    private ?string $token = null;

    /**
     * @return self
     */
    public static function asDomain(): self
    {
        return (new self())
            ->setType(VerificationType::asDomain());
    }

    /**
     * @return self
     */
    public static function asToken(): self
    {
        return (new self())
            ->setType(VerificationType::asToken());
    }

    /**
     * @return VerificationType|null
     */
    public function getType(): ?VerificationType
    {
        return $this->type;
    }

    /**
     * @param VerificationType $type
     * @return self
     */
    public function setType(VerificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

}
