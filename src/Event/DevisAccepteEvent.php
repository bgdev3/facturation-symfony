<?php

namespace App\Event;

use App\Entity\Devis;
use Symfony\Contracts\EventDispatcher\Event;

class DevisAccepteEvent extends Event
{
    public function __construct(private Devis $devis){}

    public function getDevis(): Devis
    {
        return $this->devis;
    }
}