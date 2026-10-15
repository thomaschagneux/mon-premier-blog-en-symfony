<?php

namespace App\Controller;

use App\Enum\FlashType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

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

    protected function renderFormResponse(string $view, array $parameters, FormInterface $form): Response
    {
        $response = null;
        if ($form->isSubmitted() && !$form->isValid()) {
            $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render($view, $parameters, $response);
    }
}
