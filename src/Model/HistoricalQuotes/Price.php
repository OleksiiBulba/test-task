<?php

declare(strict_types=1);

namespace App\Model\HistoricalQuotes;

class Price
{
    private \DateTimeImmutable $date;

    private float $open;

    private string $high;

    private string $low;

    private string $close;

    private string $volume;

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function setOpen(float $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getHigh(): string
    {
        return $this->high;
    }

    public function setHigh(string $high): self
    {
        $this->high = $high;

        return $this;
    }

    public function getLow(): string
    {
        return $this->low;
    }

    public function setLow(string $low): self
    {
        $this->low = $low;

        return $this;
    }

    public function getClose(): string
    {
        return $this->close;
    }

    public function setClose(string $close): self
    {
        $this->close = $close;

        return $this;
    }

    public function getVolume(): string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }
}
