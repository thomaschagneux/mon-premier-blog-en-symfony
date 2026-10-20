<?php

namespace App\Service\DataTable;

use App\Components\DataTables\Column;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserDataTableService extends AbstractDataTableService
{
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
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
                fn (User $user) => \sprintf(
                    '%s %s',
                    $this->getActionLinks(['id' => $user->getId()], 'user_profile', 'user_edit'),
                    $this->getDeleteForm($user)
                ),
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

    private function getDeleteForm(User $user): string
    {
        $userId = $user->getId();
        $url = $this->urlGenerator->generate('user_delete', ['id' => $userId], UrlGeneratorInterface::ABSOLUTE_PATH);
        $token = $this->csrfTokenManager->getToken('delete-user-'.$userId)->getValue();

        return \sprintf(
            '<form method="post" action="%s" class="d-inline"><input type="hidden" name="_token" value="%s"><button type="submit" class="btn btn-sm btn-danger">Supprimer</button></form>',
            htmlspecialchars($url, ENT_QUOTES),
            htmlspecialchars($token, ENT_QUOTES)
        );
    }
}
