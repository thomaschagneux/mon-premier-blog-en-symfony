<?php

namespace App\Controller;

use App\Enum\FlashType;
use App\Utils\RefererUrlResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    protected function getToRefererOrRoute(Request $request, string $route): string
    {
        return RefererUrlResolver::resolve($request, $this->generateUrl($route));
    }

    protected function redirectToRefererOrRoute(Request $request, string $route): Response
    {
        return new RedirectResponse($this->getToRefererOrRoute($request, $route));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function renderFormResponse(string $view, array $parameters, FormInterface $form): Response
    {
        $response = null;
        if ($form->isSubmitted() && !$form->isValid()) {
            $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render($view, $parameters, $response);
    }
}
