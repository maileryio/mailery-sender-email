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
     * @return bool
     */
    public function verify(EmailSender $sender): bool
    {
        $result = $this->wrapVerify($sender);

        (new EntityWriter($this->orm))->write([$sender]);

        return $result;
    }

    /**
     * @param EmailSender $sender
     * @return bool
     */
    private function wrapVerify(EmailSender $sender): bool
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

        if ($domain !== null && $sender->isSameDomain($domain->getDomain())) {
            return $sender
                ->verifyDomain($domain)
                ->isActive();
        }

        $this->sendVerificationEmail($sender);

        return $sender->isActive();
    }

    /**
     * @param EmailSender $sender
     * @return void
     */
    private function sendVerificationEmail(EmailSender $sender): void
    {
        $sender->setVerificationToken(
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
    }
}
