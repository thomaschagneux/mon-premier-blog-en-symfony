<?php

namespace App\Service\DataTable;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\DataTables\AbstractDataTableService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class UserDataTableService extends AbstractDataTableService
{
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
    )
    {
        $this->columnMappings = [
            'name' => 'Nom',
            'email' => 'email',
        ];
        parent::__construct($twig, $urlGenerator);
    }

    public function getTableContent(): string
    {
        $users = $this->userRepository->findAll();
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                'email'      => $user->getEmail(),
            ];
        }

        return $this->renderTable($rows);
    }

    protected function getColumnClass(string $key): string
    {
        $customClasses = [
            'name'       => 'column-name',
            'email'      => 'column-email',
        ];

        return $customClasses[$key] ?? parent::getColumnClass($key);
    }

}

