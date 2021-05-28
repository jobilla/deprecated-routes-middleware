# Deprecated Routes

Laravel package to provide a middleware to mark any route as deprecated.

## Install

Run the following command in your project folder to add the package to your project.

    composer require jobilla/deprecated-routes-middleware

_(Optional)_ Add the following line to `$routeMiddleware` array in your `app/Http/Kernel.php`.

```php
'deprecated' => \Jobilla\DeprecatedRoutes\Http\Middlewares\DeprecatedRoute::class,
```

## Usage

### Using the middleware on route groups

You can define the deprecation on route group level.

```php
Route::prefix('api/v3')
    ->middleware('deprecated:2021-03-22')
    ->group(function () {
        // Your route definitions here.
    });
```

### Using the middleware on individual routes

You can define the middleware on a single route.

```php
Route::get('old/endpoint', OldEnpointController::class)->middleware('deprecated:2021-03-22');
```

### Using the middleware on controllers

You can define the middleware inside a controller class.

```php
class OldEnpointController extends Controller
{
    public function __construct()
    {
        $this->middleware('deprecated:2021-03-22');
    }
}
```
