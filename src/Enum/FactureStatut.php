<?php

namespace App\Enum;

enum FactureStatut: string
{
    case Brouillon = 'brouillon';
    case Envoyee = 'envoyée';
    case Payee = 'payée';
    case EnRetard = 'en_retard';
    case Annulee = 'annulée';

    public function label(): string
    {
        return match($this) {
            self::Brouillon => 'Brouillon',
            self::Envoyee => 'Envoyée',
            self::Payee => 'Payée',
            self::EnRetard => 'En retard',
            self::Annulee => 'Annulée',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Brouillon => 'gray',
            self::Envoyee => 'blue',
            self::Payee => 'green',
            self::EnRetard => 'red',
            self::Annulee => 'orange',
        };
    }
}