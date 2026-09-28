<?php

namespace App\Services;

use App\Entity\Facture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class RelanceFactureService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer
    ){}

    public function relancer(Facture $facture): void
    {
        $email =(new TemplatedEmail())
            ->to($facture->getClient()->getEmail())
            ->subject('Rappel : facture ' . $facture->getNumero() . ' en attente de règlement')
            ->htmlTemplate('emails/relance_facture.html.twig')
            ->context(['facture' => $facture]);

            $this->mailer->send($email);

            $facture->setDernierRelanceAt(new \DateTimeImmutable())
                    ->setNombreRelance($facture->getNombreRelance() + 1);

            $this->em->flush();
    }
}