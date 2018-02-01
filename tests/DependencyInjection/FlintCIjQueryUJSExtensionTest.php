<?php

declare(strict_types=1);

namespace FlintCI\jQueryUJSBundle\Tests\DependencyInjection;

use FlintCI\jQueryUJSBundle\DependencyInjection\FlintCIjQueryUJSExtension;
use FlintCI\jQueryUJSBundle\Security\Csrf\UjsCsrfManager;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class FlintCIjQueryUJSExtensionTest extends AbstractExtensionTestCase
{
    public function testLoad(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(UjsCsrfManager::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new FlintCIjQueryUJSExtension(),
        ];
    }
}
