<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Form;

use FormManager\Factory as F;
use FormManager\Form;
use Mailery\Brand\Entity\Brand;
use Mailery\Brand\BrandLocatorInterface as BrandLocator;
use Mailery\Sender\Email\Entity\Sender;
use Mailery\Sender\Email\Repository\SenderRepository;
use Mailery\Sender\Email\Service\SenderCrudService;
use Mailery\Sender\Email\ValueObject\SenderValueObject;
use Symfony\Component\Validator\Constraints;

class SenderForm extends Form
{
    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @var Sender|null
     */
    private ?Sender $sender = null;

    /**
     * @var SenderRepository
     */
    private SenderRepository $senderRepo;

    /**
     * @var SenderCrudService
     */
    private SenderCrudService $senderCrudService;

    /**
     * @param BrandLocator $brandLocator
     * @param SenderRepository $senderRepo
     * @param SenderCrudService $senderCrudService
     */
    public function __construct(
        BrandLocator $brandLocator,
        SenderRepository $senderRepo,
        SenderCrudService $senderCrudService
    ) {
        $this->brand = $brandLocator->getBrand();
        $this->senderRepo = $senderRepo->withBrand($this->brand);
        $this->senderCrudService = $senderCrudService;

        parent::__construct($this->inputs());
    }

    /**
     * @param string $csrf
     * @return \self
     */
    public function withCsrf(string $value, string $name = '_csrf'): self
    {
        $this->offsetSet($name, F::hidden($value));

        return $this;
    }

    /**
     * @param Sender $sender
     * @return self
     */
    public function withSender(Sender $sender): self
    {
        $this->sender = $sender;
        $this->offsetSet('', F::submit('Update'));

        $this['name']->setValue($sender->getName());

        return $this;
    }

    /**
     * @return Sender|null
     */
    public function save(): ?Sender
    {
        if (!$this->isValid()) {
            return null;
        }

        $valueObject = SenderValueObject::fromForm($this)
            ->withBrand($this->brand);

        if (($sender = $this->sender) === null) {
            $sender = $this->senderCrudService->create($valueObject);
        } else {
            $this->senderCrudService->update($sender, $valueObject);
        }

        return $sender;
    }

    /**
     * @return array
     */
    private function inputs(): array
    {
        return [
            'name' => F::text('Name')
                ->addConstraint(new Constraints\NotBlank()),

            '' => F::submit($this->sender === null ? 'Create' : 'Update'),
        ];
    }
}
