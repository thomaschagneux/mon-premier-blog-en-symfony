<?php

namespace App\Service\DataTable;

use App\Components\Column;
use App\Entity\User;

class UserDataTableService
{
    public function getColumns(): array
    {
        $columns = [];

        $columns[] = new Column(
            name: 'fullName',
            title: 'Nom complet',
            data: fn (User $user) => $user->getFirstName(),
            options: [
                'class' => 'fw-bold', // Classe CSS pour le gras
            ],
        );

        $columns[] = new Column(
            name: 'email',
            title: 'Email',
            data: fn (User $user) => $user->getEmail(),
            options: [
                'class' => 'text-center',
                'style' => 'color: #007bff;', // Style inline
            ],
        );

        $columns[] = new Column(
            name: 'roles',
            title: 'Rôles',
            data: fn (User $user) => implode(', ', $user->getRoles()),
            options: [
                'class' => 'text-muted small',
                'data-toggle' => 'tooltip', // Attribut HTML personnalisé
                'title' => 'Rôles de l\'utilisateur',
            ],
        );

        return $columns;
    }

    public function getOptions(): array
    {
        return [];
    }
}
