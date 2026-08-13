<?php

namespace App\Enum;

enum DevisStatut: string
{
    case Brouillon = 'brouillon';
    case Envoye = 'envoyé';
    case Accepte = 'accepté';
    case Refuse = 'refusé';
    case Expire = 'expiré';

    public function label(): string
    {
        return match($this) {
            self::Brouillon => 'Brouillon',
            self::Envoye => 'Envoyé',
            self::Accepte => 'Accepté',
            self::Refuse => 'Refusé',
            self::Expire => 'Expiré',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Brouillon => 'gray',
            self::Envoye => 'blue',
            self::Accepte => 'green',
            self::Refuse => 'red',
            self::Expire => 'orange',
        };
    }
}