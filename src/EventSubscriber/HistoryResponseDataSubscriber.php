<?php

namespace App\EventSubscriber;

use App\Company\Provider\CompanyProviderInterface;
use App\Event\HistoryRequestFinished;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

readonly class HistoryResponseDataSubscriber implements EventSubscriberInterface
{
    const DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private MailerInterface $mailer,
        private CompanyProviderInterface $companyProvider,
        private string $fromAddress,
        private string $fromName
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HistoryRequestFinished::class => 'sendEmailNotification',
        ];
    }

    public function sendEmailNotification(HistoryRequestFinished $event): void
    {
        try {
            $this->mailer->send(
                (new Email())
                    ->from(new Address($this->fromAddress, $this->fromName))
                    ->to($event->getRequest()->getEmail())
                    ->subject($this->companyProvider->searchBySymbol($event->getRequest()->getSymbol())->getName())
                    ->text(sprintf(
                        'From %s to %s',
                        $event->getRequest()->getStartDate()->format(self::DATE_FORMAT),
                        $event->getRequest()->getEndDate()->format(self::DATE_FORMAT)
                    ))
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Could not send an email notification', $e->getCode(), $e);
        }
    }
}
