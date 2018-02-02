# jQuery UJS bundle

Symfony bundle adapter for [jQuery-ujs](https://github.com/rails/jquery-ujs) and CSRF protection.

[![Latest Stable Version](https://poser.pugx.org/flintci/jquery-ujs-bundle/v/stable)](https://packagist.org/packages/flintci/jquery-ujs-bundle)
[![Latest Unstable Version](https://poser.pugx.org/flintci/jquery-ujs-bundle/v/unstable)](https://packagist.org/packages/flintci/jquery-ujs-bundle)
[![License](https://poser.pugx.org/flintci/jquery-ujs-bundle/license)](https://packagist.org/packages/flintci/jquery-ujs-bundle)

[![Total Downloads](https://poser.pugx.org/flintci/jquery-ujs-bundle/downloads)](https://packagist.org/packages/flintci/jquery-ujs-bundle)
[![Monthly Downloads](https://poser.pugx.org/flintci/jquery-ujs-bundle/d/monthly)](https://packagist.org/packages/flintci/jquery-ujs-bundle)
[![Daily Downloads](https://poser.pugx.org/flintci/jquery-ujs-bundle/d/daily)](https://packagist.org/packages/flintci/jquery-ujs-bundle)

[![Build Status](https://travis-ci.org/flintci/jquery-ujs-bundle.svg?branch=master)](https://travis-ci.org/flintci/jquery-ujs-bundle)
[![Coverage Status](https://coveralls.io/repos/flintci/jquery-ujs-bundle/badge.svg?branch=master)](https://coveralls.io/r/flintci/jquery-ujs-bundle?branch=master)

## Installation

Install the bundle with composer:

``` bash
composer require flintci/jquery-ujs-bundle
```

## Configuration

Enable the bundle. It is already done if you use Symfony Flex.

``` php
// config/bundles.php

return [
    FlintCI\jQueryUJSBundle\FlintCIjQueryUJSBundle::class => ['all' => true],
];
```

Add the `metas.html.twig` template file on the `<head>` part:

```twig
{# base.html.twig #}

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        {% include '@FlintCIjQueryUJS/metas.html.twig' %}
    </head>
    {# ... #}
</html>
```

Finally, install jquery-ujs
with [Yarn](https://yarnpkg.com/en/package/jquery-ujs)
or [NPM](https://www.npmjs.com/package/jquery-ujs)
and include the [rails.js](https://github.com/rails/jquery-ujs/blob/master/src/rails.js) file.

Example on a `app.js` file using WebPack:

```javascript
import 'jquery-ujs';
```

Then, you are good to go!

## Usage

Start using jquery-ujs by writing this special link:

```twig
<a href="{{ path('account_delete') }}" data-method="delete" data-confirm="Are you sure?">
```

Then you can manually verify the CSRF validity on the controller:

```php
namespace App\Controller;

use FlintCI\jQueryUJSBundle\Security\Csrf\UjsCsrfManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/account")
 */
final class AccountController extends Controller
{
    /**
     * @Route("/")
     * @Method("DELETE")
     */
    public function deleteAction(UjsCsrfManager $ujsCsrfManager): Response
    {
        if (!$ujsCsrfManager->isTokenValid()) {
            throw new BadRequestHttpException('Invalid token.');
        }
        
        // ...
    }
}
```

Or directly with the annotation:

```php
namespace App\Controller;

use FlintCI\jQueryUJSBundle\Annotations\UjsCsrf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/account")
 */
final class AccountController extends Controller
{
    /**
     * @Route("/")
     * @Method("DELETE")
     * @UjsCsrf
     */
    public function deleteAction(): Response
    {
        // Nothing to check here. A bad request excpetion will be thrown if the token is invalid.
    }
}
```
