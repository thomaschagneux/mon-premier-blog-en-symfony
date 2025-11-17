<?php

namespace App\Controller;

use App\Enum\FlashType;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function addSuccess(string $message): void
    {
        $this->addFlash(
            FlashType::SUCCESS->value,
            $message
        );
    }

    protected function addError(string $message): void
    {
        $this->addFlash(
            FlashType::ERROR->value,
            $message
        );
    }
}
