<?php

declare(strict_types=1);

namespace App\Model;

use App\Validator\Symbol;
use Symfony\Component\Validator\Constraints as Assert;

class HistoryRequestData
{
    #[Assert\NotBlank]
    #[Symbol]
    private string $symbol;

    #[Assert\Range(maxMessage: 'Start date should be less or equal than end date.', maxPropertyPath: 'endDate')]
    private ?\DateTimeInterface $startDate = null;

    #[Assert\Range(notInRangeMessage: 'End date should be between start date and today date.', minPropertyPath: 'startDate', max: 'today')]
    private ?\DateTimeInterface $endDate = null;

    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
