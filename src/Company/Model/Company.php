<?php

declare(strict_types=1);

namespace App\Company\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Company implements \Stringable
{
    #[SerializedName('Symbol')]
    private string $symbol;

    #[SerializedName('Company Name')]
    private string $name;

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getCompanyName(): string
    {
        return $this->name;
    }

    public function setCompanyName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->symbol, $this->name);
    }
}
