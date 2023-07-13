<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieVoter extends Voter
{
    public const REMOVE = 'remove';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::REMOVE
            && $subject instanceof Movie;
    }

    /**
     * @param string $attribute
     * @param Movie $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        if ($user->isEqualTo($subject->getCreatedBy())) {
            return true;
        }

        return false;
    }
}
