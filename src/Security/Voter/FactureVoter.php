<?php

namespace App\Security\Voter;

use App\Entity\Facture;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class FactureVoter extends Voter
{
    public const LIST = 'INVOICE_LIST';
    public const CREATE = 'INVOICE_CREATE';
    public const EDIT = 'INVOICE_EDIT';
    public const VIEW = 'INVOICE_VIEW';
    public const DELETE = 'INVOICE_DELETE';
    public const RELANCE = 'INVOICE_RELANCE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::LIST, self::CREATE]) || 
        in_array($attribute,  [self::EDIT, self::VIEW, self::DELETE, self::RELANCE])
            && $subject instanceof \App\Entity\Facture;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
       
        // Si l'utilisateur n'est pas connu, acces interdit
        if (!$user instanceof User) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

         // Pas de facture en jeu : on répond avant d'y toucher
         if (in_array($attribute, [self::LIST, self::CREATE], true)) 
            return true;
    

         /** @var Facture $facture */
        $facture = $subject;

          if ($attribute === self::RELANCE && !$facture->isRelancable()) {
            $vote?->addReason('Une facture ne peut être relancée.');
            return false;
        }

         // Règle métier, valable pour tout le monde, admin compris
        if ($attribute === self::EDIT && !$facture->isEditable()) {
            $vote?->addReason('Une facture émise ne peut plus être modifiée.');
            return false;
        }

        if ($attribute === self::DELETE && !$facture->isDeletable()) {
            $vote?->addReason('Une facture émise ne peut pas être supprimée.');
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) 
                return true;

        if ($facture->getClient()->getUser() !== $user) {
            return false;
        }
        
       return match ($attribute) {
            self::LIST, self::CREATE, self::VIEW, self::RELANCE => true,
            self::EDIT => $facture->isEditable(),
            self::DELETE => $facture->isDeletable(),
            default => false,
        };
    }
}
