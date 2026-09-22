<?php 
namespace App\Enum;

enum ConditionsStatus: string
{
    case Comptant = 'comptant';
    case Paiments_30_jours = 'paiement 30 jours';
    case A_reception = 'A réception';

    public function label(): string
    {
        return match($this) {
            self::Comptant => 'Comptant',
            self::Paiments_30_jours => 'Paiement 30 jours',
            self::A_reception => 'À réception',
        };
    }
}