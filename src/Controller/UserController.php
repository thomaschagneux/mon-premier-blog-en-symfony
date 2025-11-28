<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\DataTable\UserDataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserDataTableService $userDataTableService,
    ) {
    }

    #[Route('/', name: 'user_list')]
    public function index(): Response
    {
        $table = $this->userDataTableService->getTableContent();

        return $this->render('user/users_list.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/profile/{id}', name: 'user_profile')]
    public function showUser(User $user): Response
    {
        return $this->render('user/user_profile.html.twig', [
            'user' => $user,
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
