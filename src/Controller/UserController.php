<?php

namespace App\Controller;

use App\Components\DataTables;
use App\Repository\UserRepository;
use App\Service\DataTable\UserDataTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly DataTables $dataTables,
        private readonly UserDataTableService $userDataTableService,
    ) {
    }

    #[Route('/', name: 'user_list')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        $userDataTable = $this->dataTables
            ->setColumns($this->userDataTableService->getColumns())
            ->setEntities($users)
            ->setOptions($this->userDataTableService->getOptions())
            ->render();

        return $this->render('user/users_list.html.twig', [
            'userDataTable' => $userDataTable,
        ]);
    }
}
