<?php

namespace Mailery\Sender\Email\Service;

use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Repository\DomainRepository;
use Mailery\Sender\Email\Model\VerificationToken;
use Mailery\Common\Setting\GeneralSettingGroup;
use Yiisoft\Mailer\MailerInterface;
use Cycle\ORM\ORMInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Mailery\Sender\Email\Model\VerificationType;

class SenderVerifyService
{
    /**
     * @var string|null
     */
    private ?string $verificationToken = null;

    /**
     * @param ORMInterface $orm
     * @param MailerInterface $mailer
     * @param GeneralSettingGroup $settingGroup
     * @param DomainRepository $domainRepo
     */
    public function __construct(
        private ORMInterface $orm,
        private MailerInterface $mailer,
        private GeneralSettingGroup $settingGroup,
        private DomainRepository $domainRepo
    ) {}

    /**
     * @param string $verificationToken
     * @return self
     */
    public function withVerificationToken(string $verificationToken): self
    {
        $new = clone $this;
        $new->verificationToken = $verificationToken;

        return $new;
    }

    /**
     * @param EmailSender $sender
     * @return void
     */
    public function sendVerificationEmail(EmailSender $sender): void
    {
        $sender
            ->setVerificationType(VerificationType::asToken())
            ->setVerificationToken(
            (new VerificationToken())
                ->withLength(32)
                ->generate()
        );

        $message = $this->mailer->compose(
                'verify',
                [
                    'sender' => $sender,
                ]
            )
            ->withFrom($this->settingGroup->getNoReplyEmail()->getValue())
            ->withTo($sender->getEmail())
            ->withSubject('Please verify your email address')
        ;

        $this->mailer->send($message);

        (new EntityWriter($this->orm))->write([$sender]);
    }

    /**
     * @param EmailSender $sender
     * @return bool
     */
    public function verify(EmailSender $sender): bool
    {
        $result = $this->verifyByDomain($sender);

        if (!$result) {
            $result = $this->verifyByEmail($sender);
        }

        return $result;
    }

    /**
     * @param EmailSender $sender
     * @return bool
     */
    private function verifyByEmail(EmailSender $sender): bool
    {
        if ($sender->getStatus()->isActive()) {
            return true;
        }

        if ($this->verificationToken === null) {
            return false;
        }

        $result = $sender
            ->verifyVerificationToken($this->verificationToken)
            ->getStatus()
            ->isActive();

        (new EntityWriter($this->orm))->write([$sender]);

        return $result;
    }

    /**
     * @param EmailSender $sender
     * @return bool
     */
    private function verifyByDomain(EmailSender $sender): bool
    {
        if ($sender->getStatus()->isActive()) {
            return true;
        }

        $result = (function (EmailSender $sender): bool {
            $domains = $this->domainRepo
                ->withBrand($sender->getBrand())
                ->findAll();

            foreach ($domains as $domain) {
                /** @var Domain $domain */
                if ($sender->isSameDomain($domain->getDomain())) {
                    return $sender
                        ->verifyDomain($domain)
                        ->getStatus()
                        ->isActive();
                }
            }

            return false;
        })($sender);

        (new EntityWriter($this->orm))->write([$sender]);

        return $result;
    }
}
