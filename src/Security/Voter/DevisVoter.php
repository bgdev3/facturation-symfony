<?php

namespace App\Security\Voter;

use App\Entity\Devis;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class DevisVoter extends Voter
{
    public const LIST = 'DEVIS_LIST';
    public const CREATE = 'DEVIS_CREATE';
    public const EDIT = 'DEVIS_EDIT';
    public const VIEW = 'DEVIS_VIEW';
    public const DELETE= 'DEVIS_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
     
        return in_array($attribute, [self::LIST, self::CREATE]) ||
         in_array($attribute,[self::EDIT, self::VIEW, self::DELETE] )
            && $subject instanceof \App\Entity\Devis;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if(!$user instanceof User)
            return false;

        // Si l'utilisateur n'est pas connu, accès interdit
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Devis $devis */
        $devis = $subject;

        if ($devis->getClient()->getUser() !== $user)
            return false;

        return match ($attribute) {
            self::LIST, self::CREATE, self::VIEW =>true,
            self::EDIT, self::DELETE => $devis->isEditable(),
            default => false,
        };

    }
}
