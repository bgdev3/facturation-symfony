<?php 
namespace App\Enum;

enum PaiementStatus: string
{
    case Virement = 'virement';
    case CB = 'cb';
    case Cheque = 'cheque';
    case Espece = 'espèce';

    public function label(): string
    {
        return match($this) {
            self::Virement => 'Virement',
            self::CB => 'CB',
            self::Cheque => 'Chèque',
            self::Espece => 'Espèce',
        };
    }
}