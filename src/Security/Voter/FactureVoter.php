<?php

namespace App\Security\Voter;

use App\Entity\Facture;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class FactureVoter extends Voter
{
    public const LIST = 'INVOICE_LIST';
    public const CREATE = 'INVOICE_CREATE';
    public const EDIT = 'INVOICE_EDIT';
    public const VIEW = 'INVOICE_VIEW';
    public const DELETE = 'INVOICE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::LIST, self::CREATE]) || 
        in_array($attribute,  [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Facture;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User)
            return false;

        // Si l'utilisateur n'est pas connu, acces interdit
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

         /** @var Facture $facture */
        $facture = $subject;

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) 
                return true;

             if ($facture->getClient()->getUser() !== $user) {
            return false;
        }
        
       return match ($attribute) {
            self::LIST, self::CREATE, self::VIEW => true,
            self::EDIT, self::DELETE => $facture->isEditable(),
            default => false,
        };
    }
}
