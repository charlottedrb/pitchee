<?php

namespace App\Security\Voter;

use App\Entity\Card;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CardVoter extends Voter
{
    const DELETE = 'delete';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE, self::EDIT])
            && $subject instanceof \App\Entity\Card;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $card = $subject;

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Card) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute){
            case self::EDIT:
                return $this->canEdit($card, $user);
            case self::DELETE:
                return $this->canDelete($card, $user);
        }

        return false;
    }

    private function canDelete(Card $card, User $user): bool
    {
        return $user === $card->getUser();
    }

    private function canEdit(Card $card, User $user): bool
    {
        return $user === $card->getUser();
    }
}
