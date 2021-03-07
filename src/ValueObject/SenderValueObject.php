<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\ValueObject;

use Mailery\Brand\Entity\Brand;
use Mailery\Sender\Email\Form\SenderForm;

class SenderValueObject
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param SenderForm $form
     * @return self
     */
    public static function fromForm(SenderForm $form): self
    {
        $new = new self();

        $new->name = $form['name']->getValue();

        return $new;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Brand
     */
    public function getBrand(): Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @return self
     */
    public function withBrand(Brand $brand): self
    {
        $new = clone $this;
        $new->brand = $brand;

        return $new;
    }
}
