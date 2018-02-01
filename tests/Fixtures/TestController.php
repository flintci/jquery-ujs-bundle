<?php

declare(strict_types=1);

namespace FlintCI\jQueryUJSBundle\Tests\Fixtures;

use FlintCI\jQueryUJSBundle\Annotations\UjsCsrf;

final class TestController
{
    public function indexAction(): void
    {
    }

    /**
     * @UjsCsrf
     */
    public function deleteAction(): void
    {
    }
}
