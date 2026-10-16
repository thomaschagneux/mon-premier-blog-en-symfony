<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\DataTable\UserDataTableService;
use App\Service\User\UserCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserDataTableService $userDataTableService,
        private readonly UserCreator $userCreator,
        private readonly TranslatorInterface $translator,
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
    public function createUser(Request $request): Response
    {
        $user = new User();
        $addUserForm = $this->createForm(UserType::class, $user);
        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted()) {
            if ($addUserForm->isValid()) {
                $this->userCreator->create($user);
                $this->addSuccess($this->translator->trans('success.users.add'));

                return $this->redirectToRoute('user_list');
            }
        }

        return $this->renderFormResponse('user/user_form_add.html.twig', [
            'add_user_form' => $addUserForm->createView(),
        ], $addUserForm);
    }

    #[Route('/edit/{id}', name: 'user_edit')]
    public function editUser(User $user, Request $request): Response
    {
        $editUserForm = $this->createForm(UserType::class, $user, [
            'form_mode' => 'edit',
        ]);
        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted()) {
            if ($editUserForm->isValid()) {
                $password = $editUserForm->get('password')->getData();
                $this->userCreator->update($user, is_string($password) ? $password : null);
                $this->addSuccess($this->translator->trans('success.users.update'));

                return $this->redirectToRoute('user_list');
            }
        }

        return $this->renderFormResponse('user/user_form_edit.html.twig', [
            'edit_user_form' => $editUserForm->createView(),
            'user' => $user,
        ], $editUserForm);
    }

    #[Route('/delete/{id}', name: 'user_delete')]
    public function deleteUser(User $user): Response
    {
        $this->userCreator->delete($user);
        $this->addSuccess($this->translator->trans('success.users.delete'));

        return $this->redirectToRoute('user_list');
    }
}
