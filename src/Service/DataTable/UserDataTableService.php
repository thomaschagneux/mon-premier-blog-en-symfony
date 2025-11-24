<?php

namespace App\Service\DataTable;

use App\Components\DataTables\Column;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserDataTableService extends AbstractDataTableService
{
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($twig, $urlGenerator, $translator);
    }

    protected function getEntityName(): string
    {
        return 'users';
    }

    protected function getColumnsConfig(): array
    {
        return [
            new Column(
                'action',
                null,
                ''
            ),
            new Column(
                'full_name',
                fn (User $user) => $user->getFullName(),
                'column-name'
            ),
            new Column(
                'email',
                fn (User $user) => $user->getEmail(),
                'column-email'
            ),
        ];
    }

    protected function getData(): array
    {
        return $this->userRepository->findAll();
    }

    public function getTableContent(): string
    {
        return $this->renderTableContent();
    }
}
