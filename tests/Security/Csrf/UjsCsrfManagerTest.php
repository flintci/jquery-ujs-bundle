<?php

declare(strict_types=1);

namespace FlintCI\jQueryUJSBundle\Tests\Security\Csrf;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use FlintCI\jQueryUJSBundle\Security\Csrf\UjsCsrfManager;
use FlintCI\jQueryUJSBundle\Tests\Fixtures\TestController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

final class UjsCsrfManagerTest extends TestCase
{
    /**
     * @var CsrfTokenManager
     */
    private $csrfTokenManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var UjsCsrfManager
     */
    private $ujsCsrfManager;

    /**
     * @var TestController
     */
    private $testController;

    protected function setUp(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
        $this->csrfTokenManager = new CsrfTokenManager(
            null,
            new SessionTokenStorage(new Session(new MockArraySessionStorage()))
        );
        $this->ujsCsrfManager = new UjsCsrfManager(
            $this->csrfTokenManager,
            new AnnotationReader()
        );
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber($this->ujsCsrfManager);
        $this->testController = new TestController();
    }

    public function testWithAnnotation(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('UJS CSRF token is invalid.');

        $this->eventDispatcher->dispatch(
            KernelEvents::CONTROLLER,
            new FilterControllerEvent(
                new KernelForTest('dev', false),
                [$this->testController, 'deleteAction'],
                new Request(),
                null
            )
        );
    }

    public function testWithValidToken(): void
    {
        $this->eventDispatcher->dispatch(
            KernelEvents::CONTROLLER,
            new FilterControllerEvent(
                new KernelForTest('dev', false),
                [$this->testController, 'deleteAction'],
                new Request([], [
                    '_ujs_token' => $this->csrfTokenManager->getToken('ujs')->getValue(),
                ]),
                null
            )
        );

        $this->assertTrue($this->ujsCsrfManager->isTokenValid());
    }

    public function testWithoutAnnotation(): void
    {
        $this->eventDispatcher->dispatch(
            KernelEvents::CONTROLLER,
            new FilterControllerEvent(
                new KernelForTest('dev', false),
                [$this->testController, 'indexAction'],
                new Request(),
                null
            )
        );

        $this->assertFalse($this->ujsCsrfManager->isTokenValid());
    }
}
