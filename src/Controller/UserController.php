<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'user_list')]
    public function index(): Response
    {
        return $this->render('user/users_list.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_new')]
    public function createUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = new User();
        $addUserForm = $this->createForm(UserType::class, $user);

        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $password = $user->getPassword();

            if (null !== $password) {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
            }

            $this->addSuccess('User has been created.');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user_form_add.html.twig', [
            'add_user_form' => $addUserForm->createView(),
        ]);
    }
}
