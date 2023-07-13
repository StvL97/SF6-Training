<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void {
        $rolesUser = ['ROLE_USER'];
        $rolesAdmin = ['ROLE_USER', 'ROLE_ADMIN'];

        $userData = [
            [
                'username' => 'slk',
                'password' => '123456',
                'roles' => $rolesAdmin
            ],
            [
                'username' => 'noAdmin',
                'password' => '123456',
                'roles' => $rolesUser
            ],
            [
                'username' => 'noAdmin2',
                'password' => '123456',
                'roles' => $rolesUser
            ]
        ];

        foreach ($userData as $data) {

            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);

            $user
                ->setUsername($data['username'])
                ->setRoles($data['roles'])
                ->setPassword($hashedPassword);

            $manager->persist($user);
            $this->addReference('user_' . $user->getUsername(), $user);
        }

        $manager->flush();
    }
}
