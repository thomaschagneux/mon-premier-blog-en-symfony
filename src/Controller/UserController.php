<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\DataTable\UserDataTableService;
use App\Service\User\UserCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    public function index(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addError($this->translator->trans('error.users.access_denied'));

            return $this->redirectToRefererOrRoute($request, 'home');
        }
        $table = $this->userDataTableService->getTableContent();

        return $this->render('user/users_list.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/profile/{id}', name: 'user_profile')]
    public function showUser(Request $request, User $user): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $user) {
            $this->addError($this->translator->trans('error.users.access_denied'));

                return $this->redirectToRefererOrRoute($request, 'home');
            }

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
        $resticted = false;
        
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser() !== $user) {
                $this->addError($this->translator->trans('error.users.access_denied'));

                return $this->redirectToRefererOrRoute($request, 'home');
            } else {
                $resticted = true;
            }
        }

        $editUserForm = $this->createForm(UserType::class, $user, [
            'form_mode' => 'edit',
            'restricted' => $resticted,
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

    #[Route('/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(User $user, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addError($this->translator->trans('error.users.access_denied'));

            return $this->redirectToRefererOrRoute($request, 'home');
        }
        $csrfToken = $request->request->get('_token');
        if (!\is_string($csrfToken) || !$this->isCsrfTokenValid('delete-user-'.$user->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $this->userCreator->delete($user);
        $this->addSuccess($this->translator->trans('success.users.delete'));

        return $this->redirectToRoute('user_list');
    }
}
