<?php

declare(strict_types=1);

namespace FlintCI\jQueryUJSBundle\Security\Csrf;

use Doctrine\Common\Annotations\Reader;
use FlintCI\jQueryUJSBundle\Annotations\UjsCsrf;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class UjsCsrfManager implements EventSubscriberInterface
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var bool
     */
    private $tokenValid = false;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, Reader $annotationReader)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        $this->tokenValid = $this->csrfTokenManager->isTokenValid(
            new CsrfToken('ujs', $request->request->get('_ujs_token'))
        );

        list($controller, $action) = $event->getController();
        $method = new \ReflectionMethod($controller, $action);
        $annotation = $this->annotationReader->getMethodAnnotation($method, UjsCsrf::class);
        if ($annotation instanceof UjsCsrf && !$this->isTokenValid()) {
            throw new BadRequestHttpException('UJS CSRF token is invalid.');
        }
    }

    public function isTokenValid(): bool
    {
        return $this->tokenValid;
    }
}
