<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class HistoryDataRequest
{
    #[Assert\NotBlank]
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

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
