<?php

namespace Mailery\Sender\Email\Service;

use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Repository\DomainRepository;
use Mailery\Sender\Email\Entity\Embedded\Verification;
use Mailery\Sender\Email\Model\VerificationToken;
use Mailery\Common\Setting\GeneralSettingGroup;
use Yiisoft\Mailer\MailerInterface;
use Cycle\ORM\ORMInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Mailery\Sender\Field\SenderStatus;

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
        $sender->setVerification(
            Verification::asToken()
                ->setToken((new VerificationToken())->withLength(32)->generate())
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
        if ($sender->getStatus()->isActive()) {
            return true;
        }

        if ($this->verificationToken !== null
            && ($verification = $sender->getVerification()) !== null
            && $verification->getType()->isToken()
            && $verification->getToken() === $this->verificationToken
        ) {
            $sender->setStatus(SenderStatus::asActive());
        }

        if (!$sender->getStatus()->isActive()) {
            $domains = $this->domainRepo
                ->withBrand($sender->getBrand())
                ->findAll();

            foreach ($domains as $domain) {
                /** @var Domain $domain */
                if (!$domain->isVerified()
                    || !$sender->isSameDomain($domain->getDomain())
                ) {
                    continue;
                }

                $sender->setStatus(SenderStatus::asActive())
                    ->setVerification(Verification::asDomain());

                break;
            }
        }

        (new EntityWriter($this->orm))->write([$sender]);

        return $sender->getStatus()->isActive();
    }

}
