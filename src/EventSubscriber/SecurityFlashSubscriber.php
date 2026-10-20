<?php

namespace App\EventSubscriber;

use App\Enum\FlashType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityFlashSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translatorInterface,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => ['onLogout', -1],
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $this->addFlashMessage(
            $event->getRequest(),
            $this->translatorInterface->trans('security.login.success')
        );
    }

    public function onLogout(LogoutEvent $event): void
    {
        $this->addFlashMessage(
            $event->getRequest(),
            $this->translatorInterface->trans('security.logout.success')
        );
    }

    private function getFlashBag(Request $request): FlashBagInterface
    {
        $session = $request->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface) {
            throw new \LogicException('The session does not support flash messages.');
        }

        return $session->getFlashBag();
    }

    private function addFlashMessage(Request $request, string $message): void
    {
        $this->getFlashBag($request)->add(FlashType::SUCCESS->value, $message);
    }
}
