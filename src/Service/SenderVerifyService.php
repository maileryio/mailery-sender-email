<?php

namespace Mailery\Sender\Email\Service;

use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Repository\DomainRepository;
use Mailery\Sender\Email\Model\VerificationToken;
use Yiisoft\Mailer\Mailer;

class SenderVerifyService
{
    /**
     * @var Mailer
     */
    private Mailer $mailer;

    /**
     * @var DomainRepository
     */
    private DomainRepository $domainRepo;

    /**
     * @var string|null
     */
    private ?string $verificationToken = null;

    /**
     * @param Mailer $mailer
     * @param DomainRepository $domainRepo
     */
    public function __construct(
        Mailer $mailer,
        DomainRepository $domainRepo
    ) {
        $this->mailer = $mailer;
        $this->domainRepo = $domainRepo;
    }

    /**
     * @param string $verificationToken
     */
    public function withVerificationToken(string $verificationToken)
    {
        $this->verificationToken = $verificationToken;
    }

    /**
     * @param EmailSender $sender
     * @return bool
     */
    public function verify(EmailSender $sender): bool
    {
        if ($sender->isActive()) {
            return true;
        }

        if ($this->verificationToken !== null) {
            return $sender
                ->verifyVerificationToken($this->verificationToken)
                ->isActive();
        }

        /** @var Domain $domain */
        $domain = $this->domainRepo
            ->withBrand($sender->getBrand())
            ->findOne();

        $active = $sender
            ->verifyDomain($domain)
            ->isActive();

        if ($active) {
            return true;
        }

        $sender->setVerificationToken(
            (new VerificationToken())
                ->withLength(32)
                ->generate()
        );

        $this->mailer->

        var_dump($sender->getVerificationToken());exit;
    }
}